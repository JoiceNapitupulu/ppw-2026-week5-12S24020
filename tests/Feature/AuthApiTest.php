<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private User $pustakawan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pustakawan = User::create([
            'name'     => 'Pustakawan Del',
            'email'    => 'pustakawan@del.ac.id',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_login_successful_returns_sanctum_bearer_token(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'       => 'pustakawan@del.ac.id',
            'password'    => 'password123',
            'device_name' => 'postman-test',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token_type',
                'access_token',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
        $this->assertEquals('Bearer', $response->json('data.token_type'));
        $this->assertNotEmpty($response->json('data.access_token'));
    }

    public function test_login_with_invalid_credentials_returns_401_rfc7807(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'pustakawan@del.ac.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJson([
            'status' => 401,
            'title'  => 'Invalid Credentials',
        ]);
    }

    public function test_login_validation_failure_returns_422_rfc7807(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'not-an-email',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJsonStructure([
            'type',
            'title',
            'status',
            'detail',
            'instance',
            'errors' => ['email', 'password'],
        ]);
    }

    public function test_get_me_profile_with_valid_token(): void
    {
        $token = $this->pustakawan->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'id'    => $this->pustakawan->id,
                'email' => 'pustakawan@del.ac.id',
            ],
        ]);
    }

    public function test_logout_revokes_token_successfully(): void
    {
        $token = $this->pustakawan->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Reset guard state in memory so subsequent request re-authenticates token from DB
        $this->app['auth']->forgetGuards();

        // Token should now be revoked, next request returns 401
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(401);
        $meResponse->assertHeader('Content-Type', 'application/problem+json');
    }
}
