<?php

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

class BaseController extends Controller
{
    const PAGE_DEFAULT_COUNT = 10;

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
     * - Sets the application/json Content-Type
     *
     * @param $data             Payload to be encoded into JSON
     * @param int $statusCode   A valid HTTP response code
     * @return Response
     */
    protected function createApiResponse($data, $statusCode = Response::HTTP_OK)
    {
        $json = $this->serialize($data);
        return new Response($json, $statusCode, array(
            'Content-Type' => 'application/json'
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
