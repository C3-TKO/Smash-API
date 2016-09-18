<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class TokenControllerTest extends ApiTestCase
{
    public function testCreateToken() {
        $this->createUser('thomas.kolar', 'top-secret');

        $response = $this->client->post('/tokens', [
            'auth' => ['thomas.kolar', 'top-secret']
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->asserter()->assertResponsePropertyExists(
            $response,
            'token'
        );
    }
}
