<?php

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

class BaseController extends Controller
{
    /**
     * @param Form $form
     * @return Response
     */
    protected function throwApiProblemValidationException(Form $form) {
        $apiProblem = new ApiProblem(
            Response::HTTP_BAD_REQUEST,
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $apiProblem->set('errors', $this->getErrorsFromForm($form));

        throw new ApiProblemException($apiProblem);
    }

    /**
     * @param Request $request
     * @param Form $form
     */
    protected function processForm(Request $request, Form $form)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);

            throw new ApiProblemException($apiProblem);
        }

        $clearMissing = $request->getMethod() !== 'PATCH';
        $form->submit($data, $clearMissing);
    }

    /**
     * Retrieves the errors for all form fields recursively
     *
     * @param Form $form
     * @return array
     */
    protected function getErrorsFromForm(Form $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof Form) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

    /**
     * Creates an api response
     * - Serializes the payload in $data into json
     * - Defaults to HTTP OK (200) if not specified otherwise
     * - Sets the application/hal+json Content-Type
     *
     * @param $data             Payload to be encoded into JSON
     * @param int $statusCode   A valid HTTP response code
     * @return Response
     */
    protected function createApiResponse($data, $statusCode = Response::HTTP_OK)
    {
        $json = $this->serialize($data);
        return new Response($json, $statusCode, array(
            'Content-Type' => 'application/hal+json'
        ));
    }

    /**
     * Serializes the payload in $data into json
     *
     * @param $data     Payload to be encoded into JSON
     * @return mixed
     */
    protected function serialize($data)
    {
        return $this->container->get('jms_serializer')
            ->serialize($data, 'json');
    }
}
