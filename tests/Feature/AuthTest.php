<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_get_token()
    {
        $payload = [
            'email' => 'test@mail.ru',
            'phone' => '+79990001122',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('api/register', $payload);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'user' => ['id', 'email', 'phone', 'created_at'],
            'roles',
            'token',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@mail.ru']);

        $user = User::where('email', 'test@mail.ru')->first();
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_register_user_validation()
    {
        $payload = [
            'phone' => '',
            'email' => 'test@mail.ru',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];


        $response = $this->postJson('api/register', $payload);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('phone');
    }
}
