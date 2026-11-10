<?php

namespace Tests\Feature;

use Tests\TestCase;

class CorsTest extends TestCase
{
    public function test_api_responses_include_cors_headers_for_the_frontend_origin(): void
    {
        $response = $this->withHeader('Origin', 'https://dataspect-six.vercel.app')
            ->postJson('/api/login', []);

        $response->assertHeader('Access-Control-Allow-Origin', 'https://dataspect-six.vercel.app');
    }

    public function test_api_responses_include_cors_headers_for_local_development(): void
    {
        $response = $this->withHeader('Origin', 'http://localhost:3000')
            ->postJson('/api/login', []);

        $response->assertHeader('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
}
