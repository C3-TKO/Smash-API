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
    public function getTeamByIdShouldReturnAValidTeam()
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
    public function getNotExistingTeamShouldThrow404()
    {
        $data = ['name' => 'TestTeamNameUpdated'];

        $response = $this->client->get('/api/teams/1', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->assertAccessToNotExistingEntity($response, 'team', 1);
    }

    /**
     * @test
     */
    public function putNameShouldUpdateTeamName()
    {
        $this->createPlayers(['ACME', 'INC.']);

        $em = $this->getEntityManager();
        $playerA = $em->getRepository('AppBundle:Player')->findOneById(1);
        $playerB = $em->getRepository('AppBundle:Player')->findOneById(2);

        $this->createTeam($playerA, $playerB);

        $data = ['name' => 'TestTeamNameUpdated'];

        $response = $this->client->put('/api/teams/1/name', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/hal+json', $response->getHeader('Content-Type')[0]);
        $this->assertTeamModelSpecifictaion($response);

        $this->asserter()->assertResponsePropertyEquals($response, 'id', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_player_a', 1);
        $this->asserter()->assertResponsePropertyEquals($response, 'id_player_b', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'name', 'TestTeamNameUpdated');
        $this->asserter()->assertResponsePropertyEquals($response, '_links.self.href', $this->adjustUri('/api/teams/1'));
    }

    /**
     * @test
     */
    public function putNameOnNotExistingTeamShouldThrow404()
    {
        $data = ['name' => 'TestTeamNameUpdated'];

        $response = $this->client->put('/api/teams/1/name', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)
        ]);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->assertAccessToNotExistingEntity($response, 'team', 1);
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

    private function assertAccessToNotExistingEntity(ResponseInterface $response, $entityName, $id)
    {
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'detail',
            'status',
            'type',
            'title'
        ));

        $this->asserter()->assertResponsePropertyEquals($response, 'detail', sprintf('No %s found with id %u', $entityName, $id));
        $this->asserter()->assertResponsePropertyEquals($response, 'status', '404');
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'about:blank');
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Not Found');
    }
}
