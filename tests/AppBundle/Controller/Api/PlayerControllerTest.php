<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class PlayerControllerTest extends ApiTestCase
{
    /**
     * @test
     */
    public function postAValidPlayerShouldCreateANewPlayerEntity()
    {
        $data = array(
            'name' => 'ACME'
        );

        $response = $this->client->post('/players', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals($response->getHeader('Location'), '/players/1');
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'name'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', 'ACME');
    }

    /**
     * @test
     */
    public function getPlayerShouldRetrieveASinglePlayer()
    {
        $this->createPlayer(['name' => 'ACME']);

        $response = $this->client->get('/players/1');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test123
     */
    public function getPlayersShouldRetrieveACollectionOfAllPlayers()
    {
        $response = $this->client->get('/players');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
