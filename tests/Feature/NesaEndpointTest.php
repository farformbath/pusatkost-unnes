<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Ai\Models\NesaConversation;
use App\Domain\Ai\Models\NesaMessage;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\ListingImage;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NesaEndpointTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('ai.base_url', 'https://discovery-api.intern-ai.org.cn/v1');
        Config::set('ai.api_key', 'test-api-key');
        Config::set('ai.model', 'intern-latest');
        Config::set('ai.max_tokens', 1024);
        Config::set('ai.temperature', 0.7);
        Config::set('ai.timeout_seconds', 15);

        $this->owner = User::query()->create([
            'name' => 'Owner Kost',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    private function createSampleListing(string $name, string $slug, GenderType $gender, string $area, int $price): Listing
    {
        $listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => $name,
            'slug' => $slug,
            'gender_type' => $gender,
            'area' => $area,
            'address' => 'Jl. Sekaran Raya No. 10',
            'description' => 'Kost nyaman dan strategis dekat kampus UNNES',
            'status' => ListingStatus::PUBLISHED,
        ]);

        Room::query()->create([
            'listing_id' => $listing->id,
            'name' => 'Kamar Standar',
            'price_monthly' => $price,
            'total_rooms' => 5,
            'available_rooms' => 2,
            'status' => RoomStatus::AVAILABLE,
        ]);

        ListingImage::query()->create([
            'listing_id' => $listing->id,
            'file_path' => 'images/listings/'.$slug.'.jpg',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        return $listing;
    }

    public function test_guest_can_send_chat_message_to_nesa_api_endpoint_and_receive_response(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'id' => 'chatcmpl-test',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Halo! Saya NESA, asisten cerdas kos UNNES. Ada yang bisa dibantu?',
                        ],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => ['total_tokens' => 30],
            ], 200),
        ]);

        $response = $this->postJson('/api/nesa/chat', [
            'message' => 'Halo NESA',
            'session_token' => 'guest-session-123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'reply',
                'session_token',
                'recommended_listings',
            ])
            ->assertJson([
                'reply' => 'Halo! Saya NESA, asisten cerdas kos UNNES. Ada yang bisa dibantu?',
                'session_token' => 'guest-session-123',
                'recommended_listings' => [],
            ]);

        $this->assertDatabaseHas('nesa_conversations', [
            'session_token' => 'guest-session-123',
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('nesa_messages', [
            'role' => 'user',
            'content' => 'Halo NESA',
        ]);

        $this->assertDatabaseHas('nesa_messages', [
            'role' => 'assistant',
            'content' => 'Halo! Saya NESA, asisten cerdas kos UNNES. Ada yang bisa dibantu?',
        ]);
    }

    public function test_it_validates_chat_request_payload(): void
    {
        // Missing message
        $response = $this->postJson('/api/nesa/chat', [
            'message' => '',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        // Message exceeds 1000 chars
        $response = $this->postJson('/api/nesa/chat', [
            'message' => str_repeat('a', 1001),
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        // Session token exceeds 100 chars
        $response = $this->postJson('/api/nesa/chat', [
            'message' => 'Halo',
            'session_token' => str_repeat('b', 101),
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['session_token']);
    }

    public function test_guest_can_send_message_without_session_token_and_receives_generated_token(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'id' => 'chatcmpl-test',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Halo kak! Saya NESA.',
                        ],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => ['total_tokens' => 20],
            ], 200),
        ]);

        $response = $this->postJson('/api/nesa/chat', [
            'message' => 'Halo tanpa token',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'reply',
                'session_token',
                'recommended_listings',
            ]);

        $token = $response->json('session_token');
        $this->assertIsString($token);
        $this->assertNotEmpty($token);

        $this->assertDatabaseHas('nesa_conversations', [
            'session_token' => $token,
            'user_id' => null,
        ]);
    }

    public function test_authenticated_user_retains_conversation_association(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'id' => 'chatcmpl-test',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Halo mahasiswa!',
                        ],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => ['total_tokens' => 25],
            ], 200),
        ]);

        $student = User::query()->create([
            'name' => 'Mahasiswa Aktif',
            'email' => 'mahasiswa@mail.unnes.ac.id',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($student)->postJson('/api/nesa/chat', [
            'message' => 'Halo dari akun mahasiswa',
            'session_token' => 'auth-student-session',
        ]);

        $response->assertOk()
            ->assertJson([
                'session_token' => 'auth-student-session',
            ]);

        $this->assertDatabaseHas('nesa_conversations', [
            'session_token' => 'auth-student-session',
            'user_id' => $student->id,
        ]);
    }

    public function test_it_validates_history_request_requires_session_token(): void
    {
        $response = $this->getJson('/api/nesa/history');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['session_token']);

        $response = $this->getJson('/api/nesa/history?session_token='.str_repeat('x', 101));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['session_token']);
    }

    public function test_history_returns_empty_arrays_when_conversation_not_found(): void
    {
        $response = $this->getJson('/api/nesa/history?session_token=nonexistent-session');

        $response->assertOk()
            ->assertExactJson([
                'messages' => [],
                'recommended_listings' => [],
            ]);
    }

    public function test_fetching_conversation_history_filters_public_messages_and_hydrates_recommended_listings(): void
    {
        $listing = $this->createSampleListing('Kost Putri Sekaran Cantik', 'kost-putri-sekaran-cantik', GenderType::PUTRI, 'Sekaran', 850000);

        $conversation = NesaConversation::query()->create([
            'session_token' => 'history-session-test',
            'title' => 'Rekomendasi Kos Sekaran',
        ]);

        // 1. User message
        NesaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Tolong carikan kos putri di Sekaran',
            'created_at' => now()->subMinutes(3),
        ]);

        // 2. Tool message (internal agentic call - should be hidden from UI history)
        NesaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => 'tool',
            'tool_call_id' => 'call_1',
            'content' => json_encode(['results' => [['id' => $listing->id]]]),
            'created_at' => now()->subMinutes(2),
        ]);

        // 3. Assistant message with recommended listings
        NesaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Berikut kos putri yang cocok di Sekaran:',
            'recommended_listing_ids' => [(string) $listing->id],
            'created_at' => now()->subMinute(),
        ]);

        $response = $this->getJson('/api/nesa/history?session_token=history-session-test');

        $response->assertOk()
            ->assertJsonStructure([
                'messages' => [
                    '*' => ['id', 'role', 'content', 'created_at'],
                ],
                'recommended_listings' => [
                    '*' => ['id', 'name', 'slug', 'area', 'gender', 'starting_price', 'primary_image_url'],
                ],
            ]);

        $messages = $response->json('messages');
        $this->assertCount(2, $messages);
        $this->assertSame('user', $messages[0]['role']);
        $this->assertSame('Tolong carikan kos putri di Sekaran', $messages[0]['content']);
        $this->assertSame('assistant', $messages[1]['role']);
        $this->assertSame('Berikut kos putri yang cocok di Sekaran:', $messages[1]['content']);

        $recommendations = $response->json('recommended_listings');
        $this->assertCount(1, $recommendations);
        $this->assertSame((string) $listing->id, $recommendations[0]['id']);
        $this->assertSame('Kost Putri Sekaran Cantik', $recommendations[0]['name']);
        $this->assertSame('kost-putri-sekaran-cantik', $recommendations[0]['slug']);
        $this->assertSame('Sekaran', $recommendations[0]['area']);
        $this->assertSame('putri', $recommendations[0]['gender']);
        $this->assertSame(850000, $recommendations[0]['starting_price']);
    }
}
