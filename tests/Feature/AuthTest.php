<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    public function test_auth_user()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('api/me');

        $response->assertStatus(200);
    }

    public function test_auth_user_validation()
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
