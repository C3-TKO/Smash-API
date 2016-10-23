<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class GameControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createUser(self::USERNAME_TEST_USER);
    }

    /**
     * @test
     */
    public function postAValidGameShouldCreateANewGameEntity()
    {
        list(
            $playerA,
            $playerB,
            $playerC,
            $playerD,
            $teamA,
            $teamB,
            $round
            ) = $this->prepareAValidGameInDatabase();

        $data = [
            'id_round' => $round->getId(),
            'id_team_a' => $teamA->getId(),
            'id_team_b' => $teamB->getId(),
            'team_a_score' => 21,
            'team_b_score' => 19,
        ];

        $response = $this->client->post('/api/games', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->assertStringEndsWith('/api/rounds/1', $response->getHeader('Location')[0]);
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'id_round',
            'id_team_a',
            'id_team_b',
            'team_a_score',
            'team_b_score'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_round', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_team_a', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_team_b', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'team_a_score', 21);
        $this->asserter()->assertResponsePropertyEquals($response, 'team_b_score', 19);


        // Only one player should be in database
        $em = $this->getEntityManager();
        $players = $em->getRepository('AppBundle:Game')->findAll();
        $this->assertEquals(1, count($players));
    }

    private function prepareAValidGameInDatabase() {
        $this->createPlayers(['Player_A', 'Player_B', 'Player_C', 'Player_D']);

        $em = $this->getEntityManager();
        $playerA = $em->getRepository('AppBundle:Player')->findOneById(1);
        $playerB = $em->getRepository('AppBundle:Player')->findOneById(2);
        $playerC = $em->getRepository('AppBundle:Player')->findOneById(3);
        $playerD = $em->getRepository('AppBundle:Player')->findOneById(4);

        $this->createTeam($playerA, $playerB);
        $this->createTeam($playerC, $playerD);

        $teamA = $em->getRepository('AppBundle:Team')->findOneById(1);
        $teamB = $em->getRepository('AppBundle:Team')->findOneById(2);

        $this->createRounds(['1980-04-30']);

        $round = $em->getRepository('AppBundle:Round')->findOneById(1);

        return [
            $playerA,
            $playerB,
            $playerC,
            $playerD,
            $teamA,
            $teamB,
            $round
        ];
    }
}
