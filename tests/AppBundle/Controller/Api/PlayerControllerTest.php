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
        $this->assertStringEndsWith('/players/1', $response->getHeader('Location'));
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'name'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', 'ACME');

        // Only one player should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();
        $this->assertEquals(1, count($players));
    }

    /**
     * @test
     */
    public function getPlayerShouldRetrieveASinglePlayer()
    {
        $this->createPlayers(['ACME']);

        $response = $this->client->get('/players/1');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
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
    public function getPlayersShouldRetrieveACollectionOfAllPlayers()
    {
        $this->createPlayers(['ACME', 'INC.']);

        $response = $this->client->get('/players');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyIsArray($response, 'players');
        $this->asserter()->assertResponsePropertyCount($response, 'players', 2);
    }

    /**
     * @test
     */
    public function putPlayerShouldUpdatePlayer()
    {
        $this->createPlayers(['ACME']);

        $data = array(
            'id' => 1,
            'name' => 'INC.'
        );

        $response = $this->client->put('/players/1', [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'name'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', 'INC.');
    }

    /**
     * @test
     */
    public function deletePlayerShouldDeleteAPlayer()
    {
        $this->createPlayers(['ACME']);

        $response = $this->client->delete('/players/1');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // No more players should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();
        $this->assertEquals(0, count($players));
    }
}
