<?php
/**
 * Created by PhpStorm.
 * User: tomtom
 * Date: 31.08.16
 * Time: 23:45
 */

namespace AppBundle\Api;


use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    /**
     * @var ApiProblem
     */
    private $apiProblem;

    public function __construct(ApiProblem $apiProblem, $statusCode, $message, \Exception $previous = null, array $headers = array(), $code = null)
    {
        $this->apiProblem = $apiProblem;

        $statusCode = $apiProblem->getStatusCode();
        $message = $apiProblem->getTitle();
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem() {
        return $this->apiProblem;
    }

}