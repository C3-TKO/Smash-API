<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class TokenControllerTest extends ApiTestCase
{
    public function testCreateToken() {
        $this->createUser('thomas.kolar', 'top-secret');

        $response = $this->client->post('/api/tokens', [
            'auth' => ['thomas.kolar', 'top-secret']
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->asserter()->assertResponsePropertyExists(
            $response,
            'token'
        );
    }

    public function testCreateInvalidToken() {
        $this->createUser('thomas.kolar', 'top-secret');

        $response = $this->client->post('/api/tokens', [
            'auth' => ['thomas.kolar', 'doh-wrong-password']
        ]);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'about:blank');
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Unauthorized');
        $this->asserter()->assertResponsePropertyEquals($response, 'detail', 'Invalid credentials.');
    }

    public function testBadToken()
    {
        $response = $this->client->post('/api/players', [
            'body' => '[]',
            'headers' => [
                'Authorization' => 'Bearer WRONG'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
    }
}
