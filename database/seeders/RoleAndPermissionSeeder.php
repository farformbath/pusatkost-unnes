<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\User\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'manage-listings',
            'manage-rooms',
            'approve-bookings',
            'moderate-reviews',
            'manage-users',
            'book-rooms',
            'submit-reviews',
            'save-wishlists',
            'manage-payments',
            'manage-payouts',
            'approve-payouts',
            'view-payouts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles and Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => UserRole::ADMIN->value]);
        $adminRole->syncPermissions($permissions);

        $ownerRole = Role::firstOrCreate(['name' => UserRole::OWNER->value]);
        $ownerRole->syncPermissions([
            'manage-listings',
            'manage-rooms',
            'approve-bookings',
            'submit-reviews',
            'save-wishlists',
            'view-payouts',
        ]);

        $tenantRole = Role::firstOrCreate(['name' => UserRole::TENANT->value]);
        $tenantRole->syncPermissions([
            'book-rooms',
            'submit-reviews',
            'save-wishlists',
        ]);
    }
}
