<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            FacilitySeeder::class,
        ]);

        $owner = User::firstOrCreate(
            ['email' => 'sukardi@unneskost.com'],
            [
                'name' => 'Bapak H. Sukardi (Owner UNNES)',
                'phone' => '081234567890',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $owner->save();
        $owner->assignRole(UserRole::OWNER->value);

        $admin = User::firstOrCreate(
            ['email' => 'admin@unneskost.com'],
            [
                'name' => 'Admin Pusat Kost UNNES',
                'phone' => '081299998888',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole(UserRole::ADMIN->value);

        $this->call([
            ListingSeeder::class,
        ]);
    }
}
