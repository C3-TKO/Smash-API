<?php

namespace AppBundle\Tests\ControllerAPI;


use Symfony\Component\HttpFoundation\Response;

class PlayerControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testPOST()
    {
        $client = new \GuzzleHttp\Client([
            'base_url' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $data = array(
            'name' => 'ACME'
        );
        
        $response = $client->post('/players', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('name', $finishedData);
        $this->assertArrayHasKey('id', $finishedData);
    }
}
