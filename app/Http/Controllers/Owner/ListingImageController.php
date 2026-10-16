<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Domain\Listing\Actions\UploadListingImageAction;
use App\Domain\Listing\Models\Listing;
use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\UploadImageRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ListingImageController extends Controller
{
    private function resolveOwnerId(Request $request): int
    {
        $user = $request->user();
        if ($user instanceof User) {
            return (int) $user->id;
        }

        $firstUser = User::first();
        if ($firstUser) {
            return (int) $firstUser->id;
        }

        return 1;
    }

    public function store(UploadImageRequest $request, Listing $listing, UploadListingImageAction $action): RedirectResponse
    {
        $ownerId = $this->resolveOwnerId($request);
        if ($listing->owner_id !== $ownerId) {
            throw new AccessDeniedHttpException('Anda tidak berhak mengunggah gambar pada kos ini.');
        }

        /** @var UploadedFile $file */
        $file = $request->file('image');
        $isPrimary = $request->boolean('is_primary');
        $roomId = $request->input('room_id');

        $action->handle(
            listing: $listing,
            file: $file,
            isPrimary: $isPrimary,
            roomId: is_string($roomId) ? $roomId : null
        );

        return back()->with('success', 'Gambar berhasil diunggah.');
    }
}
