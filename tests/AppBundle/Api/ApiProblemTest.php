<?php

use AppBundle\Api\ApiProblem;
use Symfony\Component\HttpFoundation\Response;

class ApiProblemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function invalidTypeShouldThrowException()
    {
        new ApiProblem(Response::HTTP_BAD_REQUEST, 'invalid_type');
    }
}