<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class RoundControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createUser(self::USERNAME_TEST_USER);
    }

    /**
     * @test
     */
    public function postAValidRoundShouldCreateANewRoundEntity()
    {
        $data = array(
            'date' => '1980-04-30'
        );

        $response = $this->client->post('/api/rounds', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->assertStringEndsWith('/api/rounds/1', $response->getHeader('Location')[0]);
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'date'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'date', '1980-04-30');

        // Only one player should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Round')->findAll();
        $this->assertEquals(1, count($players));
    }

    /**
     * @test
     */
    public function postAnInvalidRoundShouldNotCreateANewPlayerEntity()
    {
        // Invalid because the mandatory attribute 'name' is invalid
        $data = array(
            'date' => 'INVALID'
        );

        $response = $this->client->post('/api/rounds', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'type',
            'title',
            'errors'
        ));
        $this->asserter()->assertResponsePropertyExists($response, 'errors.date');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.date[0]', 'This value is not valid.');
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);

        // Only one player should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Round')->findAll();
        $this->assertEmpty(count($players));
    }

    /**
     * @test
     */
    public function getRoundsShouldRespondWithACollectionOfAllRounds()
    {
        $this->createRounds([
            '1980-04-30',
            '1979-01-06',
            '2013-10-03'
        ]);

        $response = $this->client->get('/api/rounds');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertyIsArray($response, 'items');
        $this->asserter()->assertResponsePropertyCount($response, 'items', 3);
    }
}
