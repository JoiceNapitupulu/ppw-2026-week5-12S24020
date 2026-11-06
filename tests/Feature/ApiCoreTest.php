<?php

namespace Tests\Feature;

use App\Traits\ApiResponse;
use Tests\TestCase;

class ApiCoreTest extends TestCase
{
    use ApiResponse;

    public function test_unauthenticated_api_request_returns_rfc7807_json(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJsonStructure([
            'type',
            'title',
            'status',
            'detail',
            'instance',
        ]);
        $response->assertJson([
            'status' => 401,
            'title' => 'Unauthorized',
        ]);
    }

    public function test_not_found_api_request_returns_rfc7807_json(): void
    {
        $response = $this->getJson('/api/v1/non-existent-endpoint');

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJsonStructure([
            'type',
            'title',
            'status',
            'detail',
            'instance',
        ]);
        $response->assertJson([
            'status' => 404,
            'title' => 'Not Found',
        ]);
    }
}
