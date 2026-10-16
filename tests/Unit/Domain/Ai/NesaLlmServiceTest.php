<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Ai;

use App\Domain\Ai\Services\NesaLlmService;
use App\Domain\Ai\Services\NesaToolService;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NesaLlmServiceTest extends TestCase
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

    public function test_nesa_llm_service_sends_chat_completion_request_and_handles_tool_call_response(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'id' => 'chatcmpl-123',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => null,
                            'tool_calls' => [
                                [
                                    'id' => 'call_abc',
                                    'type' => 'function',
                                    'function' => [
                                        'name' => 'search_kos',
                                        'arguments' => json_encode(['gender' => 'putri', 'area' => 'Sekaran']),
                                    ],
                                ],
                            ],
                        ],
                        'finish_reason' => 'tool_calls',
                    ],
                ],
                'usage' => [
                    'total_tokens' => 85,
                ],
            ], 200),
        ]);

        $service = app(NesaLlmService::class);
        $response = $service->sendChat([
            ['role' => 'user', 'content' => 'Cari kos putri di Sekaran dong'],
        ]);

        $this->assertArrayHasKey('content', $response);
        $this->assertArrayHasKey('tool_calls', $response);
        $this->assertArrayHasKey('tokens_used', $response);
        $this->assertArrayHasKey('is_fallback', $response);

        $this->assertNull($response['content']);
        $this->assertFalse($response['is_fallback']);
        $this->assertSame(85, $response['tokens_used']);
        $this->assertIsArray($response['tool_calls']);
        $this->assertCount(1, $response['tool_calls']);

        $firstToolCall = $response['tool_calls'][0];
        $this->assertIsArray($firstToolCall['function']);
        $this->assertSame('search_kos', $firstToolCall['function']['name']);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();

            return $request->url() === 'https://discovery-api.intern-ai.org.cn/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer test-api-key')
                && isset($data['model']) && $data['model'] === 'intern-latest'
                && isset($data['temperature']) && $data['temperature'] === 0.7
                && isset($data['max_tokens']) && $data['max_tokens'] === 1024
                && isset($data['tools'])
                && is_array($data['tools']);
        });
    }

    public function test_nesa_llm_service_sends_chat_completion_without_tools_and_parses_text_content(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'id' => 'chatcmpl-456',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Halo! Saya NESA, asisten kos cerdas UNNES. Ada yang bisa dibantu?',
                        ],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => [
                    'total_tokens' => 45,
                ],
            ], 200),
        ]);

        $service = app(NesaLlmService::class);
        $response = $service->sendChat(
            [['role' => 'user', 'content' => 'Halo NESA']],
            includeTools: false
        );

        $this->assertSame('Halo! Saya NESA, asisten kos cerdas UNNES. Ada yang bisa dibantu?', $response['content']);
        $this->assertNull($response['tool_calls']);
        $this->assertSame(45, $response['tokens_used']);
        $this->assertFalse($response['is_fallback']);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();

            return ! isset($data['tools']);
        });
    }

    public function test_nesa_llm_service_handles_http_errors_gracefully_with_fallback_flag(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response(['error' => 'Internal Server Error'], 500),
        ]);

        $service = app(NesaLlmService::class);
        $response = $service->sendChat([
            ['role' => 'user', 'content' => 'Tolong rekomendasikan kos'],
        ]);

        $this->assertNull($response['content']);
        $this->assertNull($response['tool_calls']);
        $this->assertSame(0, $response['tokens_used']);
        $this->assertTrue($response['is_fallback']);
    }

    public function test_tool_declarations_follow_openai_function_calling_format(): void
    {
        $toolService = app(NesaToolService::class);
        $declarations = $toolService->getToolDeclarations();

        $this->assertCount(3, $declarations);

        $names = array_column(array_column($declarations, 'function'), 'name');
        $this->assertContains('search_kos', $names);
        $this->assertContains('get_kos_detail', $names);
        $this->assertContains('get_campus_guide', $names);

        // Check search_kos structure
        $searchKos = collect($declarations)->first(fn (array $item): bool => $item['function']['name'] === 'search_kos');
        $this->assertNotNull($searchKos);
        $props = $searchKos['function']['parameters']['properties'];
        $this->assertArrayHasKey('gender', $props);
        $this->assertArrayHasKey('area', $props);
        $this->assertArrayHasKey('max_price', $props);
        $this->assertArrayHasKey('min_price', $props);
        $this->assertArrayHasKey('facilities', $props);

        $this->assertIsArray($props['gender']);
        $this->assertIsArray($props['area']);
        $this->assertSame(['putra', 'putri', 'campur'], $props['gender']['enum']);
        $this->assertSame(['Sekaran', 'Banaran', 'Taman Siswa', 'Patemon', 'Gunungpati', 'Menoreh', 'Kalisegoro', 'Trangkil'], $props['area']['enum']);

        // Check get_kos_detail structure
        $kosDetail = collect($declarations)->first(fn (array $item): bool => $item['function']['name'] === 'get_kos_detail');
        $this->assertNotNull($kosDetail);
        $this->assertSame(['slug'], $kosDetail['function']['parameters']['required']);

        // Check get_campus_guide structure
        $campusGuide = collect($declarations)->first(fn (array $item): bool => $item['function']['name'] === 'get_campus_guide');
        $this->assertNotNull($campusGuide);
        $this->assertSame(['faculty'], $campusGuide['function']['parameters']['required']);
    }

    public function test_nesa_tool_service_executes_search_kos_tool(): void
    {
        $listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Putri Sekaran Cantik',
            'slug' => 'kost-putri-sekaran-cantik',
            'gender_type' => GenderType::PUTRI,
            'area' => 'Sekaran',
            'address' => 'Jl. Taman Siswa No. 12',
            'description' => 'Kost nyaman dekat FBS UNNES',
            'status' => ListingStatus::PUBLISHED,
        ]);

        Room::query()->create([
            'listing_id' => $listing->id,
            'name' => 'Kamar Standar AC',
            'price_monthly' => 850000,
            'total_rooms' => 5,
            'available_rooms' => 3,
            'status' => RoomStatus::AVAILABLE,
        ]);

        $facility = Facility::query()->create([
            'name' => 'WiFi Cepat',
            'slug' => 'wifi-cepat',
            'icon' => 'wifi',
            'category' => 'property',
        ]);
        $listing->facilities()->attach($facility->id);

        $toolService = app(NesaToolService::class);
        $result = $toolService->execute('search_kos', [
            'gender' => 'putri',
            'area' => 'Sekaran',
            'max_price' => 1000000,
        ]);

        $this->assertArrayHasKey('recommended_listing_ids', $result);
        $this->assertArrayHasKey('listings', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertGreaterThanOrEqual(1, $result['total']);

        $this->assertIsArray($result['recommended_listing_ids']);
        $this->assertContains((string) $listing->id, $result['recommended_listing_ids']);

        $this->assertIsArray($result['listings']);
        $firstItem = $result['listings'][0];
        $this->assertIsArray($firstItem);
        $this->assertSame('Kost Putri Sekaran Cantik', $firstItem['name']);
        $this->assertSame('Sekaran', $firstItem['area']);
    }

    public function test_nesa_tool_service_executes_get_kos_detail_tool(): void
    {
        $listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Melati Indah',
            'slug' => 'kost-melati-indah',
            'gender_type' => GenderType::PUTRI,
            'area' => 'Sekaran',
            'address' => 'Jl. Gang Cempaka No. 5',
            'description' => 'Kost putri tenang dan aman',
            'status' => ListingStatus::PUBLISHED,
        ]);

        Room::query()->create([
            'listing_id' => $listing->id,
            'name' => 'Kamar Mawar',
            'price_monthly' => 750000,
            'total_rooms' => 4,
            'available_rooms' => 2,
            'status' => RoomStatus::AVAILABLE,
        ]);

        $toolService = app(NesaToolService::class);

        // Found case
        $foundResult = $toolService->execute('get_kos_detail', ['slug' => 'kost-melati-indah']);
        $this->assertTrue($foundResult['found']);
        $this->assertIsArray($foundResult['recommended_listing_ids']);
        $this->assertContains((string) $listing->id, $foundResult['recommended_listing_ids']);
        $this->assertIsArray($foundResult['listing']);
        $this->assertSame('Kost Melati Indah', $foundResult['listing']['name']);
        $this->assertIsArray($foundResult['listing']['available_rooms']);
        $this->assertCount(1, $foundResult['listing']['available_rooms']);

        // Not found case
        $notFoundResult = $toolService->execute('get_kos_detail', ['slug' => 'slug-tidak-ada']);
        $this->assertFalse($notFoundResult['found']);
        $this->assertIsArray($notFoundResult['recommended_listing_ids']);
        $this->assertEmpty($notFoundResult['recommended_listing_ids']);
    }

    public function test_nesa_tool_service_executes_get_campus_guide_tool(): void
    {
        $toolService = app(NesaToolService::class);

        $fbsGuide = $toolService->execute('get_campus_guide', ['faculty' => 'FBS']);
        $this->assertSame('FBS', $fbsGuide['faculty']);
        $this->assertArrayHasKey('nearest_gate', $fbsGuide);
        $this->assertArrayHasKey('recommended_areas', $fbsGuide);
        $this->assertIsArray($fbsGuide['recommended_areas']);
        $this->assertContains('Sekaran', $fbsGuide['recommended_areas']);

        $ftGuide = $toolService->execute('get_campus_guide', ['faculty' => 'FT']);
        $this->assertSame('FT', $ftGuide['faculty']);
        $this->assertIsArray($ftGuide['recommended_areas']);
        $this->assertContains('Banaran', $ftGuide['recommended_areas']);

        // Unknown faculty falls back to general guide
        $unknownGuide = $toolService->execute('get_campus_guide', ['faculty' => 'UNKNOWN']);
        $this->assertArrayHasKey('message', $unknownGuide);
        $this->assertArrayHasKey('available_faculties', $unknownGuide);
    }

    public function test_nesa_tool_service_handles_unknown_tool_gracefully(): void
    {
        $toolService = app(NesaToolService::class);
        $result = $toolService->execute('non_existent_tool', []);

        $this->assertArrayHasKey('error', $result);
        $this->assertIsArray($result['recommended_listing_ids']);
        $this->assertEmpty($result['recommended_listing_ids']);
    }
}
