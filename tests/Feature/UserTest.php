<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_user(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/api/users/{$user->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', fn($email) => !empty($email));
    }

    public function test_index_users(): void
    {
        User::factory()->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)->assertJsonCount(3);
    }

    public function test_create_user(): void
    {
        $payload = [
            'phone' => "79990555343",
            'email' => 'test1@mail.ru',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'phone' => $payload['phone'],
                'email' => $payload['email'],
            ]);

        $this->assertDatabaseHas('users', [
            'phone' => $payload['phone'],
            'email' => $payload['email'],
        ]);

        $this->assertTrue(
            Hash::check('password', User::where('email', $payload['email'])->first()->password)
        );
    }

    public function test_update_user(): void
    {
        $user = User::factory()->create();

        $payload = [
            'phone' => "79990555343",
            'email' => "test111@mail.ru",
            'password' => 'password2',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => 'test111@mail.ru']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => $payload['phone'],
            'email' => $payload['email'],
        ]);

        $this->assertTrue(
            Hash::check('password2', User::find($user->id)->password),
            'Пароль не был захэширован правильно'
        );
    }

    public function test_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson('/api/users/' . $user->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('users',['id' => $user->id]);
    }

    public function test_paginate_products(): void
    {
        User::factory()->count(35)->create();

        $response = $this->getJson('/api/users?page=1');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->getJson('/api/users?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->getJson('/api/users?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(6, 'data');

    }
}
