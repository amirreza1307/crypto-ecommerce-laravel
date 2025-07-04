<?php

namespace Tests\Feature;

use Tests\TestCase;

class DebugRouteTest extends TestCase
{
    public function test_debug_route_registration(): void
    {
        // Test the exact URL being hit
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        // Debug the response
        if ($response->getStatusCode() === 404) {
            $this->fail('Route /api/register returned 404. Available routes might not include this endpoint.');
        }
        
        // Should be 422 for validation or 201 for success, not 404
        $this->assertTrue(
            in_array($response->getStatusCode(), [201, 422, 500]), 
            'Expected status 201, 422, or 500 but got ' . $response->getStatusCode()
        );
    }
}
