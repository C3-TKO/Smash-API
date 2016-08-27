<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class PlayerControllerTest extends ApiTestCase
{
    public function testPOST()
    {
        $data = array(
            'name' => 'ACME'
        );

        $response = $this->client->post('/players', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals($response->getHeader('Location'), '/players/1');

        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('name', $finishedData);
        $this->assertEquals('ACME', $finishedData['name']);
        $this->assertArrayHasKey('id', $finishedData);
        $this->assertEquals(1, $finishedData['id']);
    }
}
