<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;
use AppBundle\Entity\Team;
use AppBundle\Entity\Round;

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
            $round,
            $teamA,
            $teamB
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
        $this->assertStringEndsWith('/api/games/1', $response->getHeader('Location')[0]);
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
        $this->asserter()->assertResponsePropertyEquals($response, 'id_team_b', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'team_a_score', 21);
        $this->asserter()->assertResponsePropertyEquals($response, 'team_b_score', 19);


        // Only one player should be in database
        $em = $this->getEntityManager();
        $games = $em->getRepository('AppBundle:Game')->findAll();
        $this->assertEquals(1, count($games));
    }

    /**
     * @test
     */
    public function getGamesShouldRetrieveACollectionOfAllGames()
    {
        list(
            $round,
            $teamA,
            $teamB
        ) = $this->prepareAValidGameInDatabase();

        $this->createGames($round, $teamA, $teamB, [[21, 0], [21, 1], [21, 2]]);

        $response = $this->client->get('/api/games');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertyIsArray($response, 'items');
        $this->asserter()->assertResponsePropertyCount($response, 'items', 3);
    }

    /**
     * @test
     */
    public function getGameShouldRetrieveASingleGame()
    {
        list(
            $round,
            $teamA,
            $teamB
        ) = $this->prepareAValidGameInDatabase();

        $this->createGames($round, $teamA, $teamB, [[21, 0]]);

        $response = $this->client->get('/api/games/1');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
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
        $this->asserter()->assertResponsePropertyEquals($response, 'id_team_b', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'team_a_score', 21);
        $this->asserter()->assertResponsePropertyEquals($response, 'team_b_score', 0);
        $this->asserter()->assertResponsePropertyEquals($response, '_links.self.href', $this->adjustUri('/api/games/1'));
    }

    /**
     * @test
     */
    public function putGameShouldUpdateGame()
    {
        $this->createRounds(['1979-01-06'], false);

        // This creates games related to the round with the id value 2
        list(
            $round,
            $teamA,
            $teamB
            ) = $this->prepareAValidGameInDatabase();

        $this->createGames($round, $teamA, $teamB, [[21, 0]]);

        $data = array(
            'id_round' => 1,
            // Switching the order of the team ids
            'id_team_a' => $teamB->getId(),
            'id_team_b' => $teamA->getId(),
            // Sweitching the score
            'team_a_score' => 0,
            'team_b_score' => 21,
        );

        $response = $this->client->put('/api/games/1', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
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
        $this->asserter()->assertResponsePropertyEquals($response, 'id_team_a', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_team_b', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'team_a_score', 0);
        $this->asserter()->assertResponsePropertyEquals($response, 'team_b_score', 21);
        $this->asserter()->assertResponsePropertyEquals($response, '_links.self.href', $this->adjustUri('/api/games/1'));

        // Only one round should be in database after update
        $em = $this->getEntityManager();
        $games = $em->getRepository('AppBundle:Game')->findAll();
        $this->assertEquals(1, count($games));
    }

    /**
     * @test
     */
    public function deleteGameShouldDeleteAGame()
    {
        list(
            $round,
            $teamA,
            $teamB
            ) = $this->prepareAValidGameInDatabase();

        $this->createGames($round, $teamA, $teamB, [[21, 0]]);

        $response = $this->client->delete('/api/games/1', [
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // No more players should be in database
        $em = $this->getEntityManager();
        $games = $em->getRepository('AppBundle:Game')->findAll();
        $this->assertEquals(0, count($games));
    }

    /**
     * @test
     */
    public function testResourceNotFound()
    {
        // GET
        $response = $this->client->get('/api/games/404');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->assertAccessToNotExistingEntity($response, 'round', 404);

        // PUT
        $response = $this->client->put('/api/games/404', ['headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)]);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->assertAccessToNotExistingEntity($response, 'round', 404);

        // DELETE
        $response = $this->client->delete('/api/games/404', ['headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)]);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @return array
     */
    private function prepareAValidGameInDatabase() {
        $this->createPlayers(['Player_A', 'Player_B', 'Player_C', 'Player_D']);

        $em = $this->getEntityManager();
        $playerA = $em->getRepository('AppBundle:Player')->findOneById(1);
        $playerB = $em->getRepository('AppBundle:Player')->findOneById(2);
        $playerC = $em->getRepository('AppBundle:Player')->findOneById(3);
        $playerD = $em->getRepository('AppBundle:Player')->findOneById(4);

        $teamA = new Team();
        $teamA->setPlayerA($playerA);
        $teamA->setPlayerB($playerB);

        $teamB = new Team();
        $teamB->setPlayerA($playerC);
        $teamB->setPlayerB($playerD);

        $round = new Round();
        $round->setDate(\DateTime::createFromFormat('Y-m-d', '1980-04-30'));

        $this->getEntityManager()->persist($teamA);
        $this->getEntityManager()->persist($teamB);
        $this->getEntityManager()->persist($round);
        $this->getEntityManager()->flush($teamA);
        $this->getEntityManager()->flush($teamB);
        $this->getEntityManager()->flush($round);

        return [
            $round,
            $teamA,
            $teamB
        ];
    }
}
