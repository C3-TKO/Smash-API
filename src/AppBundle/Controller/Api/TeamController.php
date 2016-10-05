<?php

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use AppBundle\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TeamController extends BaseController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     * @Route("/api/teams", name="post_team")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        // Input validation and handling
        $team = new Team();
        $form = $this->createForm('AppBundle\Form\TeamType', $team);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        // New entity persistence
        $em = $this->getDoctrine()->getManager();
        $em->persist($team);
        $em->flush();

        /*
        $teamUrl = $this->generateUrl(
            'get_team',
            ['id' => $team->getId()]
        );*/

        $teamUrl = 'api/teams/' . $team->getId();

        // Response handling
        $response = $this->createApiResponse($team, Response::HTTP_CREATED);
        $response->headers->set('Location', $teamUrl);

        return $response;
    }
}