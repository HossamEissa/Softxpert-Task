<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('user can register with valid data', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
        ]);

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});

test('user cannot register with invalid email', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
       ->assertJsonValidationErrors(['email']);
});

test('user cannot register with existing email', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'manager@admin.com', // Already exists from seeder
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('user can login with correct credentials', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'manager@admin.com',
        'password' => '12345678',
        'device_name' => 'test-device',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'access_token',
            ]
        ]);
});

test('user cannot login with incorrect credentials', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'manager@admin.com',
        'password' => 'wrongpassword',
        'device_name' => 'test-device',
    ]);

    $response->assertStatus(404);
});

test('authenticated user can logout', function () {
    $user = User::where('email', 'manager@admin.com')->first();
    
    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson(['status' => true]);
});

test('unauthenticated user cannot access protected routes', function () {
    $response = $this->getJson('/api/tasks');

    $response->assertStatus(401);
});

test('authenticated user can access protected routes', function () {
    $user = User::where('email', 'manager@admin.com')->first();
    
    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/tasks');

    $response->assertStatus(200);
});
