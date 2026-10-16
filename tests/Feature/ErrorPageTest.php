<?php

declare(strict_types=1);

use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('non existent kos returns 404 status code', function (): void {
    $response = $this->get('/kos/slug-kos-tidak-pernah-ada-12345');

    $response->assertStatus(404);
});

test('unauthenticated user cannot access owner dashboard and is redirected to login', function (): void {
    $response = $this->get('/owner/kos');

    $response->assertRedirect('/login');
});

test('tenant cannot perform owner operations resulting in 403 forbidden', function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $tenant = User::factory()->create();
    $tenant->assignRole(UserRole::TENANT->value);

    // Tenant attempting to create a listing
    $response = $this->actingAs($tenant)->post('/owner/kos', [
        'name' => 'Kos Ilegal',
        'gender_type' => 'putri',
        'description' => 'Mencoba membuat kos tanpa izin',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    $response->assertStatus(403);
});

test('simulated 500 server error endpoint returns 500 status code', function (): void {
    Route::get('/test-server-error-simulation', function () {
        abort(500, 'Simulated Internal Server Error');
    });

    $response = $this->get('/test-server-error-simulation');

    $response->assertStatus(500);
});

test('simulated 503 maintenance endpoint returns 503 status code', function (): void {
    Route::get('/test-maintenance-simulation', function () {
        abort(503, 'Simulated Maintenance Mode');
    });

    $response = $this->get('/test-maintenance-simulation');

    $response->assertStatus(503);
});
