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
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
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
    public function postAnInvalidPlayerShouldNotCreateANewPlayerEntity()
    {
        // Invalid because the mandatory attribute 'name' is not set
        $data = array();

        $response = $this->client->post('/players', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        //$this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'type',
            'title',
            'errors'
        ));
        $this->asserter()->assertResponsePropertyExists($response, 'errors.name');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.name[0]', 'A player must have a name - except for Jaqen H\'ghar - who is actually No one');
        $this->assertEquals('application/json+problem', $response->getHeader('Content-Type'));

        // Only one player should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();
        $this->assertEmpty(count($players));
    }

    /**
     * @test
     */
    public function getPlayerShouldRetrieveASinglePlayer()
    {
        $this->createPlayers(['ACME']);

        $response = $this->client->get('/players/1');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
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
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->asserter()->assertResponsePropertyIsArray($response, 'items');
        $this->asserter()->assertResponsePropertyCount($response, 'items', 2);
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
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'name'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', 'INC.');

        // Only one player should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();
        $this->assertEquals(1, count($players));
    }

    /**
     * @test
     */
    public function putAnInvalidPlayerShouldNotUpdateAPlayerEntity()
    {
        $this->createPlayers(['ACME']);

        $data = array(
            'id' => 1,
            'name' => null
        );

        $response = $this->client->put('/players/1', [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'type',
            'title',
            'errors'
        ));
        $this->asserter()->assertResponsePropertyExists($response, 'errors.name');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.name[0]', 'A player must have a name - except for Jaqen H\'ghar - who is actually No one');
        $this->assertEquals('application/json+problem', $response->getHeader('Content-Type'));
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

    /**
     * @test
     */
    public function patchPlayerShouldUpdateAPlayer()
    {
        $this->createPlayers(['ACME']);

        $data = array(
            'name' => 'INC.'
        );

        $response = $this->client->patch('/players/1', [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
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
    public function testInvalidJSON()
    {
        $invalidBody = <<<EOF
{
    "buggyShit" : "1337
    "EvenMoahMalformedJSON": "I'm from a test!"
}
EOF;
        $response = $this->client->post('/players', [
            'body' => $invalidBody
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyContains($response, 'type', 'invalid_body_format');
    }

    /**
     * @test
     */
    public function test404Error()
    {
        $response = $this->client->get('/players/404');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('application/json+problem', $response->getHeader('Content-Type'));
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'about:blank');
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Not Found');
        $this->asserter()->assertResponsePropertyEquals($response, 'detail', 'No player found with id 404');
    }

    /**
     * @test
     */
    public function testPlayerCollectionPagination()
    {
        $playerNames = [];
        for ($i = 0; $i < 25; $i++) {
            $playerNames[] = 'TestPlayer' . $i;
        }

        $this->createPlayers($playerNames);

        // page 1
        $response = $this->client->get('/players');
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[5].id',
            6
        );
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[5].name',
            'TestPlayer5'
        );

        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);
        $this->asserter()->assertResponsePropertyEquals($response, 'total', 25);
        $this->asserter()->assertResponsePropertyExists($response, '_links.next');

        // page 2
        $nextLink = $this->asserter()->readResponseProperty($response, '_links.next');
        $response = $this->client->get($nextLink);

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'programmers[5].name',
            'TestPlayer15'
        );
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);

        $lastLink = $this->asserter()->readResponseProperty($response, '_links.last');
        $response = $this->client->get($lastLink);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'programmers[4].name',
            'TestPlayer24'
        );

        $this->asserter()->assertResponsePropertyEquals($response, 'count', 5);
    }

}
