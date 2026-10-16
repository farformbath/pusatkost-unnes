<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Ai\Models\NesaConversation;
use App\Domain\Ai\Models\NesaMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class NesaDatabaseModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_configuration_loads_proper_defaults_and_types(): void
    {
        $this->assertIsString(config('ai.base_url'));
        $this->assertNotEmpty(config('ai.base_url'));
        $this->assertIsString(config('ai.api_key'));
        $this->assertIsString(config('ai.model'));
        $this->assertNotEmpty(config('ai.model'));
        $this->assertIsInt(config('ai.max_tokens'));
        $this->assertGreaterThan(0, config('ai.max_tokens'));
        $this->assertIsFloat(config('ai.temperature'));
        $this->assertIsInt(config('ai.timeout_seconds'));
        $this->assertGreaterThan(0, config('ai.timeout_seconds'));
    }

    public function test_can_create_nesa_conversation_with_uuid_and_relationships(): void
    {
        $user = User::query()->create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@example.com',
            'password' => bcrypt('password'),
        ]);

        $conversation = NesaConversation::query()->create([
            'user_id' => $user->id,
            'session_token' => 'sess_'.Str::random(32),
            'title' => 'Cari Kost Banaran',
        ]);

        $this->assertTrue(Str::isUuid((string) $conversation->id));
        $this->assertSame('Cari Kost Banaran', $conversation->title);
        $this->assertNotNull($conversation->user);
        $this->assertSame($user->id, $conversation->user->id);

        // Test default title and guest conversation (nullable user_id)
        $guestConversation = NesaConversation::query()->create([
            'session_token' => 'guest_sess_'.Str::random(32),
        ]);

        $this->assertTrue(Str::isUuid((string) $guestConversation->id));
        $this->assertNull($guestConversation->user_id);
        $this->assertNull($guestConversation->user);
        $this->assertSame('Percakapan Baru', $guestConversation->title);
    }

    public function test_can_create_nesa_messages_with_casts_and_relationships(): void
    {
        $conversation = NesaConversation::query()->create([
            'session_token' => 'sess_test_123',
            'title' => 'Rekomendasi Kost Putri',
        ]);

        $userMessage = NesaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Carikan kost putri AC dekat FMIPA UNNES',
            'tokens_used' => 25,
            'latency_ms' => 120,
        ]);

        $this->assertTrue(Str::isUuid((string) $userMessage->id));
        $this->assertSame('user', $userMessage->role);
        $this->assertSame('Carikan kost putri AC dekat FMIPA UNNES', $userMessage->content);
        $this->assertIsInt($userMessage->tokens_used);
        $this->assertSame(25, $userMessage->tokens_used);
        $this->assertIsInt($userMessage->latency_ms);
        $this->assertSame(120, $userMessage->latency_ms);
        $this->assertInstanceOf(Carbon::class, $userMessage->created_at);

        $dummyUuid1 = (string) Str::uuid();
        $dummyUuid2 = (string) Str::uuid();

        $assistantMessage = NesaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Berikut adalah rekomendasi kost putri di Sekaran dekat FMIPA:',
            'tool_calls' => [
                [
                    'id' => 'call_123',
                    'type' => 'function',
                    'function' => [
                        'name' => 'search_listings',
                        'arguments' => '{"gender_type":"putri","facility":"AC"}',
                    ],
                ],
            ],
            'tool_call_id' => null,
            'recommended_listing_ids' => [$dummyUuid1, $dummyUuid2],
            'tokens_used' => 350,
            'latency_ms' => 1450,
        ]);

        $this->assertIsArray($assistantMessage->tool_calls);
        $this->assertCount(1, $assistantMessage->tool_calls);
        $this->assertSame('search_listings', $assistantMessage->tool_calls[0]['function']['name']);

        $this->assertIsArray($assistantMessage->recommended_listing_ids);
        $this->assertSame([$dummyUuid1, $dummyUuid2], $assistantMessage->recommended_listing_ids);

        // Verify conversation relationship
        $this->assertSame($conversation->id, $assistantMessage->conversation->id);

        // Verify messages relation on conversation
        $messages = $conversation->messages()->orderBy('created_at')->get();
        $this->assertCount(2, $messages);
        $this->assertSame($userMessage->id, $messages->first()->id);
        $this->assertSame($assistantMessage->id, $messages->last()->id);
    }

    public function test_cascade_deletes_when_conversation_or_user_is_deleted(): void
    {
        $user = User::query()->create([
            'name' => 'User Cascade',
            'email' => 'cascade@example.com',
            'password' => bcrypt('password'),
        ]);

        $conversation = NesaConversation::query()->create([
            'user_id' => $user->id,
            'session_token' => 'sess_cascade_test',
        ]);

        $message = NesaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Pesan uji cascade',
        ]);

        $this->assertDatabaseHas('nesa_conversations', ['id' => $conversation->id]);
        $this->assertDatabaseHas('nesa_messages', ['id' => $message->id]);

        // Delete user should cascade delete conversation and messages
        $user->delete();

        $this->assertDatabaseMissing('nesa_conversations', ['id' => $conversation->id]);
        $this->assertDatabaseMissing('nesa_messages', ['id' => $message->id]);
    }
}
