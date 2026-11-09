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
}
