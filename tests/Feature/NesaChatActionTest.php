<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Ai\Actions\SendNesaMessageAction;
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
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class NesaChatActionTest extends TestCase
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
            'name' => 'Pemilik Kost',
            'email' => 'pemilik@example.com',
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
            'description' => 'Kost nyaman dan strategis dekat kampus',
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

    public function test_send_nesa_message_creates_conversation_and_handles_direct_text_reply(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'id' => 'chatcmpl-1',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Halo kak! Saya NESA, ada yang bisa saya bantu tentang kos UNNES?',
                        ],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => ['total_tokens' => 30],
            ], 200),
        ]);

        $sessionToken = (string) Str::uuid();
        $action = app(SendNesaMessageAction::class);

        $result = $action->execute(
            message: 'Halo NESA!',
            sessionToken: $sessionToken,
            userId: $this->owner->id
        );

        $this->assertSame('Halo kak! Saya NESA, ada yang bisa saya bantu tentang kos UNNES?', $result['reply']);
        $this->assertSame($sessionToken, $result['session_token']);
        $this->assertSame([], $result['recommended_listings']);

        // Assert conversation exists with proper title and user_id
        $conversation = NesaConversation::query()->where('session_token', $sessionToken)->first();
        $this->assertNotNull($conversation);
        $this->assertSame('Halo NESA!', $conversation->title);
        $this->assertSame($this->owner->id, $conversation->user_id);

        // Assert 2 messages stored: user and assistant
        $messages = $conversation->messages()->orderBy('created_at')->get();
        $this->assertCount(2, $messages);

        /** @var NesaMessage $userMsg */
        $userMsg = $messages->get(0);
        $this->assertSame('user', $userMsg->role);
        $this->assertSame('Halo NESA!', $userMsg->content);

        /** @var NesaMessage $assistantMsg */
        $assistantMsg = $messages->get(1);
        $this->assertSame('assistant', $assistantMsg->role);
        $this->assertSame('Halo kak! Saya NESA, ada yang bisa saya bantu tentang kos UNNES?', $assistantMsg->content);
        $this->assertSame(30, $assistantMsg->tokens_used);
        $this->assertNotNull($assistantMsg->latency_ms);
    }

    public function test_send_nesa_message_reuses_conversation_and_limits_context_to_last_six_messages(): void
    {
        $sessionToken = (string) Str::uuid();
        $conversation = NesaConversation::query()->create([
            'session_token' => $sessionToken,
            'title' => 'Percakapan Lama',
        ]);

        // Create 8 previous messages in history (4 turns)
        for ($i = 1; $i <= 8; $i++) {
            NesaMessage::query()->create([
                'conversation_id' => $conversation->id,
                'role' => $i % 2 === 1 ? 'user' : 'assistant',
                'content' => "Pesan ke-{$i}",
                'created_at' => now()->subMinutes(10 - $i),
            ]);
        }

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Jawaban pesan terbaru.',
                        ],
                    ],
                ],
                'usage' => ['total_tokens' => 40],
            ], 200),
        ]);

        $action = app(SendNesaMessageAction::class);
        $action->execute(
            message: 'Pesan ke-9 terbaru',
            sessionToken: $sessionToken
        );

        // Verify sent messages to LLM:
        // Index 0: system prompt
        // Index 1..6: last 6 previous messages (Pesan ke-3 through Pesan ke-8)
        // Index 7: current user message (Pesan ke-9 terbaru)
        Http::assertSent(function (Request $request): bool {
            /** @var array<string, mixed> $data */
            $data = $request->data();
            /** @var list<array{role: string, content: string}> $messages */
            $messages = isset($data['messages']) && is_array($data['messages']) ? $data['messages'] : [];

            if (count($messages) !== 8) {
                return false;
            }

            $this->assertSame('system', $messages[0]['role']);
            $this->assertSame('Pesan ke-3', $messages[1]['content']);
            $this->assertSame('Pesan ke-8', $messages[6]['content']);
            $this->assertSame('Pesan ke-9 terbaru', $messages[7]['content']);

            return true;
        });
    }

    public function test_send_nesa_message_executes_tool_call_and_hydrates_recommended_listings(): void
    {
        $listing = $this->createSampleListing(
            name: 'Kost Sekaran Melati Indah',
            slug: 'kost-sekaran-melati-indah',
            gender: GenderType::PUTRI,
            area: 'Sekaran',
            price: 750000
        );

        Http::fake([
            '*/chat/completions' => Http::sequence()
                // Turn 1: LLM responds with tool call
                ->push([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => null,
                                'tool_calls' => [
                                    [
                                        'id' => 'call_search_1',
                                        'type' => 'function',
                                        'function' => [
                                            'name' => 'search_kos',
                                            'arguments' => json_encode([
                                                'gender' => 'putri',
                                                'area' => 'Sekaran',
                                                'max_price' => 1000000,
                                            ]),
                                        ],
                                    ],
                                ],
                            ],
                            'finish_reason' => 'tool_calls',
                        ],
                    ],
                    'usage' => ['total_tokens' => 60],
                ], 200)
                // Turn 2: LLM summarizes the tool results
                ->push([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Berikut adalah rekomendasi kos putri di area Sekaran dekat kampus UNNES:',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                    'usage' => ['total_tokens' => 75],
                ], 200),
        ]);

        $sessionToken = (string) Str::uuid();
        $action = app(SendNesaMessageAction::class);

        $result = $action->execute(
            message: 'Carikan kos putri di Sekaran budget max 1 juta',
            sessionToken: $sessionToken
        );

        $this->assertSame('Berikut adalah rekomendasi kos putri di area Sekaran dekat kampus UNNES:', $result['reply']);
        $this->assertSame($sessionToken, $result['session_token']);
        $this->assertCount(1, $result['recommended_listings']);

        $rec = $result['recommended_listings'][0];
        $this->assertSame((string) $listing->id, $rec['id']);
        $this->assertSame('Kost Sekaran Melati Indah', $rec['name']);
        $this->assertSame('kost-sekaran-melati-indah', $rec['slug']);
        $this->assertSame('Sekaran', $rec['area']);
        $this->assertSame('putri', $rec['gender']);
        $this->assertSame(750000, $rec['starting_price']);
        $this->assertSame('/images/listings/kost-sekaran-melati-indah.jpg', $rec['primary_image_url']);

        // Check stored assistant message
        $assistantMsg = NesaMessage::query()->where('role', 'assistant')->latest('created_at')->first();
        $this->assertNotNull($assistantMsg);
        $this->assertSame([(string) $listing->id], $assistantMsg->recommended_listing_ids);
        $this->assertSame(135, $assistantMsg->tokens_used); // 60 + 75
    }

    public function test_send_nesa_message_falls_back_gracefully_when_llm_fails(): void
    {
        $listing = $this->createSampleListing(
            name: 'Kost Banaran Putra Gagah',
            slug: 'kost-banaran-putra-gagah',
            gender: GenderType::PUTRA,
            area: 'Banaran',
            price: 600000
        );

        // LLM returns HTTP 500 error
        Http::fake([
            '*/chat/completions' => Http::response(['error' => 'Service Unavailable'], 500),
        ]);

        $sessionToken = (string) Str::uuid();
        $action = app(SendNesaMessageAction::class);

        $result = $action->execute(
            message: 'Cari info kos putra di Banaran',
            sessionToken: $sessionToken
        );

        $this->assertStringContainsString(
            'Halo kak! Saat ini sistem analitik saya sedang mengalami antrean, namun berikut beberapa rekomendasi kos di lingkar kampus UNNES yang sesuai dengan pencarian Anda:',
            $result['reply']
        );
        $this->assertSame($sessionToken, $result['session_token']);
        $this->assertNotEmpty($result['recommended_listings']);

        $ids = array_column($result['recommended_listings'], 'id');
        $this->assertContains((string) $listing->id, $ids);

        // Assistant message saved with fallback reply
        $assistantMsg = NesaMessage::query()->where('role', 'assistant')->first();
        $this->assertNotNull($assistantMsg);
        $this->assertSame(0, $assistantMsg->tokens_used);
        $this->assertNotEmpty($assistantMsg->recommended_listing_ids);
    }

    public function test_send_nesa_message_handles_campus_guide_tool_call(): void
    {
        Http::fake([
            '*/chat/completions' => Http::sequence()
                ->push([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => null,
                                'tool_calls' => [
                                    [
                                        'id' => 'call_guide_1',
                                        'type' => 'function',
                                        'function' => [
                                            'name' => 'get_campus_guide',
                                            'arguments' => json_encode(['faculty' => 'FT']),
                                        ],
                                    ],
                                ],
                            ],
                            'finish_reason' => 'tool_calls',
                        ],
                    ],
                    'usage' => ['total_tokens' => 50],
                ], 200)
                ->push([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Untuk Fakultas Teknik (FT) UNNES, gerbang terdekat adalah Gerbang Banaran. Area kos terbaik adalah Banaran dan Kalisegoro.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                    'usage' => ['total_tokens' => 65],
                ], 200),
        ]);

        $sessionToken = (string) Str::uuid();
        $action = app(SendNesaMessageAction::class);

        $result = $action->execute(
            message: 'Saya maba Fakultas Teknik UNNES, gerbang mana yang dekat dan rekomendasi daerah kosnya?',
            sessionToken: $sessionToken
        );

        $this->assertSame('Untuk Fakultas Teknik (FT) UNNES, gerbang terdekat adalah Gerbang Banaran. Area kos terbaik adalah Banaran dan Kalisegoro.', $result['reply']);
        $this->assertSame([], $result['recommended_listings']);

        $assistantMsg = NesaMessage::query()->where('role', 'assistant')->latest('created_at')->first();
        $this->assertNotNull($assistantMsg);
        $this->assertSame([], $assistantMsg->recommended_listing_ids);
        $this->assertSame(115, $assistantMsg->tokens_used);
    }

    public function test_send_nesa_message_handles_second_llm_call_fallback(): void
    {
        $this->createSampleListing(
            name: 'Kost Sekaran Asri',
            slug: 'kost-sekaran-asri',
            gender: GenderType::CAMPUR,
            area: 'Sekaran',
            price: 500000
        );

        Http::fake([
            '*/chat/completions' => Http::sequence()
                ->push([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => null,
                                'tool_calls' => [
                                    [
                                        'id' => 'call_1',
                                        'type' => 'function',
                                        'function' => [
                                            'name' => 'search_kos',
                                            'arguments' => json_encode(['area' => 'Sekaran']),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'usage' => ['total_tokens' => 40],
                ], 200)
                // Second call returns 500
                ->push(['error' => 'LLM rate limit'], 500),
        ]);

        $sessionToken = (string) Str::uuid();
        $action = app(SendNesaMessageAction::class);

        $result = $action->execute(
            message: 'Cari kos di Sekaran',
            sessionToken: $sessionToken
        );

        $this->assertStringContainsString('Berikut beberapa rekomendasi kos di sekitar kampus UNNES', $result['reply']);
        $this->assertCount(1, $result['recommended_listings']);
    }

    public function test_send_nesa_message_updates_existing_conversation_user_id_when_logged_in(): void
    {
        $sessionToken = (string) Str::uuid();

        // Conversation initially created anonymously
        $conversation = NesaConversation::query()->create([
            'session_token' => $sessionToken,
            'title' => 'Tanya Kos',
            'user_id' => null,
        ]);

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Halo lagi kak!',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $action = app(SendNesaMessageAction::class);
        $action->execute(
            message: 'Saya sudah login sekarang',
            sessionToken: $sessionToken,
            userId: $this->owner->id
        );

        $conversation->refresh();
        $this->assertSame($this->owner->id, $conversation->user_id);
    }
}
