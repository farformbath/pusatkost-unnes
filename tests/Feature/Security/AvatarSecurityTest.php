<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->user = User::create([
            'name' => 'Avatar User',
            'email' => 'avatar@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->user->assignRole(UserRole::TENANT->value);
    }

    public function test_user_can_upload_valid_avatar_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('profile.jpg', 300, 300)->size(500);

        $response = $this->actingAs($this->user)->post('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $freshAvatar = $this->user->fresh()->avatar_url;
        $this->assertNotNull($freshAvatar);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', (string) $freshAvatar));
    }

    public function test_it_rejects_invalid_mime_file_uploads(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        $response = $this->actingAs($this->user)->post('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
        $this->assertNull($this->user->fresh()->avatar_url);
    }

    public function test_it_rejects_oversized_avatar_uploads(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('huge.jpg')->size(3000); // 3MB (> 2MB limit)

        $response = $this->actingAs($this->user)->post('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
    }

    public function test_uploading_new_avatar_deletes_old_avatar_from_storage(): void
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->image('old.jpg');
        $this->actingAs($this->user)->post('/profile/avatar', ['avatar' => $oldFile]);
        $oldAvatar = $this->user->fresh()->avatar_url;
        $this->assertNotNull($oldAvatar);
        $oldPath = str_replace('/storage/', '', (string) $oldAvatar);
        Storage::disk('public')->assertExists($oldPath);

        // Upload new avatar
        $newFile = UploadedFile::fake()->image('new.jpg');
        $this->actingAs($this->user)->post('/profile/avatar', ['avatar' => $newFile]);
        $newAvatar = $this->user->fresh()->avatar_url;
        $this->assertNotNull($newAvatar);
        $newPath = str_replace('/storage/', '', (string) $newAvatar);

        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    }
}
