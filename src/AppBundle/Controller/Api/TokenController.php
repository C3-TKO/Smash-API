<?php

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

class TokenController extends BaseController
{
    /**
     * @Route("/tokens", name="post_token")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        return new Response('TOKEN!');
    }
}
