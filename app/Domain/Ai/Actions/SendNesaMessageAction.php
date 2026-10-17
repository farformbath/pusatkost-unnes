<?php

declare(strict_types=1);

namespace App\Domain\Ai\Actions;

use App\Domain\Ai\Models\NesaConversation;
use App\Domain\Ai\Models\NesaMessage;
use App\Domain\Ai\Services\NesaLlmService;
use App\Domain\Ai\Services\NesaToolService;
use App\Domain\Listing\Models\Listing;

class SendNesaMessageAction
{
    /**
     * Known UNNES student housing areas for keyword extraction.
     *
     * @var list<string>
     */
    private const array AREAS = [
        'Sekaran',
        'Banaran',
        'Taman Siswa',
        'Patemon',
        'Gunungpati',
        'Menoreh',
        'Kalisegoro',
        'Trangkil',
    ];

    public function __construct(
        private readonly NesaLlmService $llmService,
        private readonly NesaToolService $toolService,
    ) {}

    /**
     * Orchestrates user interaction with NESA AI assistant including multi-turn context,
     * tool calling, fallback handling, and response formulation.
     *
     * @return array{
     *     reply: string,
     *     session_token: string,
     *     recommended_listings: list<array{
     *         id: string,
     *         name: string,
     *         slug: string,
     *         area: string,
     *         gender: string,
     *         starting_price: int,
     *         primary_image_url: string|null
     *     }>
     * }
     */
    public function execute(string $message, string $sessionToken, ?int $userId = null): array
    {
        $startTime = hrtime(true);

        $conversation = NesaConversation::query()
            ->where('session_token', $sessionToken)
            ->first();

        if ($conversation === null) {
            $title = mb_substr(trim($message), 0, 40);
            if ($title === '') {
                $title = 'Percakapan Baru';
            }

            $conversation = NesaConversation::query()->create([
                'session_token' => $sessionToken,
                'title' => $title,
                'user_id' => $userId,
            ]);
        } elseif ($userId !== null && $conversation->user_id === null) {
            $conversation->user_id = $userId;
            $conversation->save();
        }

        // Retrieve last 6 messages prior to the current user message for multi-turn context
        $historyMessages = NesaMessage::query()
            ->where('conversation_id', $conversation->id)
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        // Save current incoming user message
        NesaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $message,
        ]);

        /** @var list<array{role: string, content: string|null, tool_calls?: list<array<string, mixed>>|null, tool_call_id?: string|null}> $messages */
        $messages = [
            [
                'role' => 'system',
                'content' => $this->getSystemPrompt(),
            ],
        ];

        foreach ($historyMessages as $history) {
            $messages[] = [
                'role' => $history->role,
                'content' => $history->content,
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        $firstResponse = $this->llmService->sendChat($messages, includeTools: true);

        // Fallback branch if LLM API fails or times out
        if ($firstResponse['is_fallback']) {
            $fallbackReply = 'Halo kak! Saat ini sistem analitik saya sedang mengalami antrean, namun berikut beberapa rekomendasi kos di lingkar kampus UNNES yang sesuai dengan pencarian Anda:';
            $searchCriteria = $this->extractSearchCriteria($message);
            $toolResult = $this->toolService->execute('search_kos', $searchCriteria);

            /** @var list<string> $listingIds */
            $listingIds = isset($toolResult['recommended_listing_ids']) && is_array($toolResult['recommended_listing_ids'])
                ? array_values(array_filter($toolResult['recommended_listing_ids'], 'is_string'))
                : [];

            $latencyMs = (int) round((hrtime(true) - $startTime) / 1e6);

            NesaMessage::query()->create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $fallbackReply,
                'recommended_listing_ids' => $listingIds,
                'tokens_used' => 0,
                'latency_ms' => $latencyMs,
            ]);

            return [
                'reply' => $fallbackReply,
                'session_token' => $conversation->session_token,
                'recommended_listings' => $this->hydrateRecommendedListings($listingIds),
            ];
        }

        /** @var list<string> $collectedListingIds */
        $collectedListingIds = [];
        $totalTokens = $firstResponse['tokens_used'] ?? 0;
        /** @var list<array<string, mixed>>|null $storedToolCalls */
        $storedToolCalls = null;

        if (! empty($firstResponse['tool_calls'])) {
            $storedToolCalls = $firstResponse['tool_calls'];

            $messages[] = [
                'role' => 'assistant',
                'content' => $firstResponse['content'],
                'tool_calls' => $firstResponse['tool_calls'],
            ];

            foreach ($firstResponse['tool_calls'] as $toolCall) {
                $callId = isset($toolCall['id']) && is_string($toolCall['id']) ? $toolCall['id'] : 'call_1';
                $function = isset($toolCall['function']) && is_array($toolCall['function']) ? $toolCall['function'] : [];
                $toolName = isset($function['name']) && is_string($function['name']) ? $function['name'] : '';

                $rawArgs = $function['arguments'] ?? [];
                if (is_string($rawArgs)) {
                    $decoded = json_decode($rawArgs, true);
                    $arguments = is_array($decoded) ? $decoded : [];
                } elseif (is_array($rawArgs)) {
                    $arguments = $rawArgs;
                } else {
                    $arguments = [];
                }

                /** @var array<string, mixed> $arguments */
                $toolResult = $this->toolService->execute($toolName, $arguments);

                if (isset($toolResult['recommended_listing_ids']) && is_array($toolResult['recommended_listing_ids'])) {
                    /** @var list<string> $ids */
                    $ids = array_values(array_filter($toolResult['recommended_listing_ids'], 'is_string'));
                    $collectedListingIds = array_merge($collectedListingIds, $ids);
                }

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $callId,
                    'content' => (string) json_encode($toolResult, JSON_UNESCAPED_UNICODE),
                ];
            }

            $collectedListingIds = array_values(array_unique($collectedListingIds));

            $messages[] = [
                'role' => 'system',
                'content' => 'Tanggapi hasil pencarian kos di atas secara ramah, ringkas, dan solutif (cukup 2-3 kalimat santai). Jangan membuat tabel markdown atau daftar panjang berulang di teks chat karena kartu kos interaktif sudah otomatis ditampilkan di bawah pesanmu.',
            ];

            $secondResponse = $this->llmService->sendChat($messages, includeTools: false);

            if ($secondResponse['is_fallback'] || empty($secondResponse['content'])) {
                $reply = 'Berikut beberapa rekomendasi kos di sekitar kampus UNNES yang sesuai dengan pencarian Anda:';
            } else {
                $reply = $secondResponse['content'];
            }

            if ($secondResponse['tokens_used'] !== null) {
                $totalTokens += $secondResponse['tokens_used'];
            }
        } else {
            $reply = $firstResponse['content'] ?? 'Halo kak! Ada yang bisa NESA bantu terkait info kos di sekitar kampus UNNES?';
        }

        $latencyMs = (int) round((hrtime(true) - $startTime) / 1e6);

        NesaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $reply,
            'tool_calls' => $storedToolCalls,
            'recommended_listing_ids' => $collectedListingIds,
            'tokens_used' => $totalTokens,
            'latency_ms' => $latencyMs,
        ]);

        return [
            'reply' => $reply,
            'session_token' => $conversation->session_token,
            'recommended_listings' => $this->hydrateRecommendedListings($collectedListingIds),
        ];
    }

    /**
     * Extracts search parameters like area and gender from user text for fallback queries.
     *
     * @return array<string, mixed>
     */
    private function extractSearchCriteria(string $message): array
    {
        $criteria = [];
        $lower = mb_strtolower($message);

        if (str_contains($lower, 'putri') || str_contains($lower, 'wanita') || str_contains($lower, 'cewek')) {
            $criteria['gender'] = 'putri';
        } elseif (str_contains($lower, 'putra') || str_contains($lower, 'pria') || str_contains($lower, 'cowok')) {
            $criteria['gender'] = 'putra';
        } elseif (str_contains($lower, 'campur')) {
            $criteria['gender'] = 'campur';
        }

        foreach (self::AREAS as $area) {
            if (str_contains($lower, mb_strtolower($area))) {
                $criteria['area'] = $area;
                break;
            }
        }

        return $criteria;
    }

    /**
     * Hydrates recommended listing models with rich metadata for frontend cards.
     *
     * @param  list<string>  $listingIds
     * @return list<array{id: string, name: string, slug: string, area: string, gender: string, starting_price: int, primary_image_url: string|null}>
     */
    private function hydrateRecommendedListings(array $listingIds): array
    {
        if (empty($listingIds)) {
            return [];
        }

        $listings = Listing::query()
            ->whereIn('id', $listingIds)
            ->with(['primaryImage', 'rooms'])
            ->get()
            ->keyBy(fn (Listing $item): string => (string) $item->id);

        $results = [];

        foreach ($listingIds as $id) {
            if (! $listings->has($id)) {
                continue;
            }

            /** @var Listing $listing */
            $listing = $listings->get($id);

            $results[] = [
                'id' => (string) $listing->id,
                'name' => (string) $listing->name,
                'slug' => (string) $listing->slug,
                'area' => (string) $listing->area,
                'gender' => $listing->gender_type->value,
                'starting_price' => $listing->starting_price,
                'primary_image_url' => $listing->primaryImage?->url,
            ];
        }

        return $results;
    }

    /**
     * System prompt establishing NESA identity, factual grounding, and polite tone.
     */
    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah NESA (UNNES Smart Assistant), asisten cerdas resmi untuk pencarian kos mahasiswa Universitas Negeri Semarang (UNNES).
Karakter dan kepribadianmu:
- Ramah, sopan, empatik, suportif, dan solutif layaknya teman mahasiswa senior UNNES.
- Menguasai informasi geografis kampus UNNES (Sekaran, Banaran, Patemon, Taman Siswa, Gunungpati, Menoreh, Trangkil, Kalisegoro) serta fakultas-fakultasnya (FBS, FT, FMIPA, FEB, FIP, FIK, FIS, FK, FH, Farmasi).
- Selalu mendasarkan rekomendasi pada fakta nyata dari data sistem. Jangan pernah berhalusinasi atau mengarang nama kos, harga, atau fasilitas yang tidak disediakan oleh data alat (tools).
- Gunakan bahasa Indonesia yang hangat dan santun (bisa memanggil pengguna dengan "kak").
- Jawablah secara ringkas, ramah, dan to-the-point (cukup 2 sampai 4 kalimat).
- JANGAN membuat tabel markdown atau menyalin ulang daftar panjang di teks chat, karena sistem web sudah otomatis menampilkan kartu interaktif lengkap dengan foto, harga, dan link di bawah pesanmu.
- Jika pengguna mencari kos atau meminta rekomendasi, gunakan fungsi search_kos atau get_kos_detail yang tersedia untuk mencari listing yang relevan.
- Jika pengguna bertanya tentang lokasi fakultas, gerbang kampus, atau zona kos yang cocok, gunakan fungsi get_campus_guide.
PROMPT;
    }
}
