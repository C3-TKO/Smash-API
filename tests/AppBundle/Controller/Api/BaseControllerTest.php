<?php

namespace AppBundle\Tests\ControllerAPI;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Test\ApiTestCase;

class BaseControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createUser(self::USERNAME_TEST_USER);
    }

    /**
     * @test
     * @dataProvider resourceNotFoundProvider
     */
    public function testResourceNotFound($methods, $routePart, $entityName)
    {
        // GET
        if (in_array('GET', $methods)) {
            $response = $this->client->get('/api/' . $routePart. '/404');
            $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
            $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
            $this->assertAccessToNotExistingEntity($response, $entityName, 404);
        }

        // PUT
        if (in_array('PUT', $methods)) {
            $response = $this->client->put('/api/' . $routePart . '/404', ['headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)]);
            $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
            $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
            $this->assertAccessToNotExistingEntity($response, $entityName, 404);
        }

        // DELETE
        if (in_array('DELETE', $methods)) {
            $response = $this->client->delete('/api/' . $routePart . '/404', ['headers' => $this->getAuthorizedHeaders(self::USERNAME_TEST_USER)]);
            $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        }
    }

    public function resourceNotFoundProvider()
    {
        return [
            [['GET', 'PUT', 'DELETE'], 'games', 'game'],
            [['GET', 'PUT', 'DELETE'], 'rounds', 'round'],
            [['GET', 'PUT', 'DELETE'], 'players', 'player'],
            [['GET'], 'teams', 'team']
        ];
    }
}
