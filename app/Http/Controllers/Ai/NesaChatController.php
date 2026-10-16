<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ai;

use App\Domain\Ai\Actions\SendNesaMessageAction;
use App\Domain\Ai\Models\NesaConversation;
use App\Domain\Ai\Models\NesaMessage;
use App\Domain\Listing\Models\Listing;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NesaChatController extends Controller
{
    /**
     * Handle incoming chat message from user / guest to NESA agent.
     */
    public function chat(Request $request, SendNesaMessageAction $action): JsonResponse
    {
        /** @var array{message: string, session_token?: string|null} $validated */
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'session_token' => ['nullable', 'string', 'max:100'],
        ]);

        $message = $validated['message'];
        $sessionToken = isset($validated['session_token']) && $validated['session_token'] !== ''
            ? $validated['session_token']
            : (string) Str::uuid();

        $user = $request->user();
        $userId = $user instanceof User ? $user->id : null;

        $result = $action->execute($message, $sessionToken, $userId);

        return response()->json([
            'reply' => $result['reply'],
            'session_token' => $result['session_token'],
            'recommended_listings' => $result['recommended_listings'],
        ]);
    }

    /**
     * Retrieve conversation history and any recommended listings for a session.
     */
    public function history(Request $request): JsonResponse
    {
        /** @var array{session_token: string} $validated */
        $validated = $request->validate([
            'session_token' => ['required', 'string', 'max:100'],
        ]);

        $sessionToken = $validated['session_token'];

        /** @var NesaConversation|null $conversation */
        $conversation = NesaConversation::query()
            ->where('session_token', $sessionToken)
            /** @param Relation<*, *, *> $query */
            ->with(['messages' => function (Relation $query): void {
                $query->orderBy('created_at', 'asc');
            }])
            ->first();

        if ($conversation === null) {
            return response()->json([
                'messages' => [],
                'recommended_listings' => [],
            ]);
        }

        // Filter messages for public display (only user and assistant roles)
        $publicMessages = $conversation->messages
            ->whereIn('role', ['user', 'assistant'])
            ->values();

        /** @var list<string> $collectedListingIds */
        $collectedListingIds = [];

        $formattedMessages = [];
        foreach ($publicMessages as $message) {
            /** @var NesaMessage $message */
            if (is_array($message->recommended_listing_ids)) {
                foreach ($message->recommended_listing_ids as $id) {
                    if (is_string($id)) {
                        $collectedListingIds[] = $id;
                    }
                }
            }

            $formattedMessages[] = [
                'id' => (string) $message->id,
                'role' => (string) $message->role,
                'content' => (string) $message->content,
                'created_at' => $message->created_at?->toISOString() ?? (string) $message->created_at,
            ];
        }

        $collectedListingIds = array_values(array_unique($collectedListingIds));
        $recommendedListings = $this->hydrateRecommendedListings($collectedListingIds);

        return response()->json([
            'messages' => $formattedMessages,
            'recommended_listings' => $recommendedListings,
        ]);
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
}
