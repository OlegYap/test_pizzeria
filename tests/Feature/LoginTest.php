<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

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

        $token = $body['token'];

        $this->assertNotEmpty($token, 'JWT token not found' . json_encode($body));

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('api/me');

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user', 'roles']);
        $response->assertJsonFragment(['email' => $user->email]);
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
