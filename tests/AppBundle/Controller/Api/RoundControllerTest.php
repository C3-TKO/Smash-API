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
}
