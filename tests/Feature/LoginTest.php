<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_user()
    {
        $password = 'test123';

        $user = User::factory()->create([
            'email' => 'test@mail.ru',
            'password' => Hash::make($password),
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->postJson(route('login'), $loginData);

        $response->assertStatus(200);

        $body = $response->json();

        $token = $body['access_token'] ?? $body['token'] ?? ($body['data']['token'] ?? null);

        $this->assertNotEmpty($token,'JWT token not found' . json_encode($body));

        $meResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('api/me');

        $response->assertStatus(200);

        $meResponse->assertJsonFragment(['email' => $user->email]);
    }

    public function test_login_user_validation()
    {
        $password = 'test123';

        $loginData = [
            'email' => '',
            'password' => $password,
        ];

        $response = $this->postJson(route('login'), $loginData);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('email');
    }
}
