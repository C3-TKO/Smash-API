<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class TeamControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createUser(self::USERNAME_TEST_USER);
    }

    /**
     * @test
     */
    public function postAValidTeamShouldCreateANewTeamEntity()
    {
        // 1) Create players for the team
        $playerNames = [];
        for ($i = 0; $i < 2; $i++) {
            $playerNames[] = 'TestPlayer' . $i;
        }

        $this->createPlayers($playerNames);

        $data = array(
            'id_player_a' => 1,
            'id_player_b' => 2,
        );

        // 2) Create a team resource via REST
        $response = $this->client->post('/api/team', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type')[0]);
        $this->assertStringEndsWith('/api/teams/1', $response->getHeader('Location')[0]);
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'name',
            'id_player_a',
            'id_player_b'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_player_a', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_player_b', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', '');

        // Only one player should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Team')->findAll();
        $this->assertEquals(1, count($players));
    }
}
