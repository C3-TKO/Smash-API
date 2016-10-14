<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;
use Psr\Http\Message\ResponseInterface;

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
    public function getTeamsShouldRetrieveATeamCollection()
    {
        $this->createPlayers(['ACME', 'INC.']);

        $em = $this->getEntityManager();
        $playerA = $em->getRepository('AppBundle:Player')->findOneById(1);
        $playerB = $em->getRepository('AppBundle:Player')->findOneById(2);

        $this->createTeam($playerA, $playerB);

        $response = $this->client->get('/api/teams');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertyIsArray($response, 'items');
        $this->asserter()->assertResponsePropertyCount($response, 'items', 1);
    }

    /**
     * @test
     */
    function getTeamByIdShouldReturnAValidTeam()
    {
        $this->createPlayers(['ACME', 'INC.']);

        $em = $this->getEntityManager();
        $playerA = $em->getRepository('AppBundle:Player')->findOneById(1);
        $playerB = $em->getRepository('AppBundle:Player')->findOneById(2);

        $this->createTeam($playerA, $playerB);

        $response = $this->client->get('/api/teams/1');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->assertTeamModelSpecifictaion($response);

        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_player_a', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_player_b', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', '');
    }

    /**
     * @test
     */
    public function putPlayerShouldUpdatePlayer()
    {
        $this->createPlayers(['ACME', 'INC.', 'GOVNO']);

        $em = $this->getEntityManager();
        $playerA = $em->getRepository('AppBundle:Player')->findOneById(1);
        $playerB = $em->getRepository('AppBundle:Player')->findOneById(2);

        $this->createTeam($playerA, $playerB);

        $data = array(
            'id_player_a' => 2, // INC.
            'id_player_b' => 3, // GOVNO
            'name'        => 'TestTeamNameUpdated'
        );

        $response = $this->client->put('/api/teams/1', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->assertTeamModelSpecifictaion($response);

        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_player_a', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_player_b', 3);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', 'TestTeamNameUpdated');
        $this->asserter()->assertResponsePropertyEquals($response, '_links.self.href', $this->adjustUri('/api/teams/1'));
    }

    private function assertTeamModelSpecifictaion(ResponseInterface $response) {
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'id',
            'id_player_a',
            'id_player_b',
            'name',
            '_links.player_a.href',
            '_links.player_b.href'
        ));
    }
}
