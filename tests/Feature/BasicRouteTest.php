<?php

namespace Tests\Feature;

use Tests\TestCase;

class BasicRouteTest extends TestCase
{
    public function test_welcome_route(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }

    public function test_api_routes_exist(): void
    {
        // Test that API routes are accessible (even if they return validation errors)
        $response = $this->postJson('/api/v1/register', []);
        
        // Should get 422 (validation error) not 404 (route not found)
        $this->assertNotEquals(404, $response->getStatusCode());
    }
}
