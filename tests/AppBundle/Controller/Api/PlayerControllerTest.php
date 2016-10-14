<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class PlayerControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createUser(self::USERNAME_TEST_USER);
    }

    /**
     * @test
     */
    public function postAValidPlayerShouldCreateANewPlayerEntity()
    {
        $data = array(
            'name' => 'ACME'
        );

        $response = $this->client->post('/api/players', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->assertStringEndsWith('/api/players/1', $response->getHeader('Location')[0]);
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

        $response = $this->client->post('/api/players', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'type',
            'title',
            'errors'
        ));
        $this->asserter()->assertResponsePropertyExists($response, 'errors.name');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.name[0]', 'A player must have a name - except for Jaqen H\'ghar - who is actually No one');
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);

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

        $response = $this->client->get('/api/players/1');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'name'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', 'ACME');
        $this->asserter()->assertResponsePropertyEquals($response, '_links.self.href', $this->adjustUri('/api/players/1'));
    }

    /**
     * @test
     */
    public function getPlayersShouldRetrieveACollectionOfAllPlayers()
    {
        $this->createPlayers(['ACME', 'INC.']);

        $response = $this->client->get('/api/players');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
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

        $response = $this->client->put('/api/players/1', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
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

        $response = $this->client->put('/api/players/1', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'type',
            'title',
            'errors'
        ));
        $this->asserter()->assertResponsePropertyExists($response, 'errors.name');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.name[0]', 'A player must have a name - except for Jaqen H\'ghar - who is actually No one');
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
    }

    /**
     * @test
     */
    public function deletePlayerShouldDeleteAPlayer()
    {
        $this->createPlayers(['ACME']);

        $response = $this->client->delete('/api/players/1', [
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // No more players should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();
        $this->assertEquals(0, count($players));
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
        $response = $this->client->post('/api/players', [
            'body' => $invalidBody,
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyContains($response, 'type', 'invalid_body_format');
    }

    /**
     * @test
     */
    public function test404Error()
    {
        $response = $this->client->get('/api/players/404');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->assertAccessToNotExistingEntity($response, 'player', 404);
    }

    /**
     * @test
     */
    public function testPlayerCollectionPaginationWithFilter()
    {
        $playerNames = [];
        for ($i = 0; $i < 25; $i++) {
            $playerNames[] = 'TestPlayer' . $i;
        }

        $this->createPlayers($playerNames);

        $this->createPlayers(['PlayerNameToBeFilteredOut']);

        // page 1
        $response = $this->client->get('/api/players?filter=TestPlayer&pageSize=10');
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
            'items[5].name',
            'TestPlayer15'
        );
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);

        // last page(3)
        $lastLink = $this->asserter()->readResponseProperty($response, '_links.last');
        $response = $this->client->get($lastLink);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[4].name',
            'TestPlayer24'
        );
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 5);

        // Just following the link for the previous page
        $prevLink = $this->asserter()->readResponseProperty($response, '_links.prev');
        $response = $this->client->get($prevLink);
        $this->assertEquals(200, $response->getStatusCode());

        // Just following the link for the first page
        $firstLink = $this->asserter()->readResponseProperty($response, '_links.first');
        $response = $this->client->get($firstLink);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRequiresAuthentication()
    {
        $response = $this->client->post('/api/players', [
            'body' => '[]'
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

}
