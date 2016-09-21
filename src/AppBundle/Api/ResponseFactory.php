<?php

namespace AppBundle\Api;


use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory
{
    public function createResponse(ApiProblem $apiProblem)
    {
        $data = $apiProblem->toArray();

        /**
         * Provide an url for the error documentation
         * @see: https://tools.ietf.org/html/draft-ietf-appsawg-http-problem-03#section-3
         */
        if ($data['type'] != 'about:blank') {
            $data['type'] = 'http://localhost:8000/docs/errors#'.$data['type'];
        }
        $response = new JsonResponse(
            $data,
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type', ApiProblem::CONTENT_TYPE);

        return $response;
    }
}