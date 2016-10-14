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
         * TODO: Uncomment when /api/teams/{id} is implemented
        $teamUrl = $this->generateUrl(
            'get_team',
            ['id' => $team->getId()]
        );*/


        $teamUrl = '/api/teams/' . $team->getId();

        // Response handling
        $response = $this->createApiResponse($team, Response::HTTP_CREATED);
        $response->headers->set('Location', $teamUrl);

        return $response;
    }


    /**
     * @Route("/api/teams", name="get_teams")
     * @Method("GET")
     */
    public function getCollectionAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Team')
            ->findAllQueryBuilder($filter);

        $paginatedCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'get_teams');

        $response = $this->createApiResponse($paginatedCollection);
        return $response;
    }

    /**
     * @Route("/api/teams/{id}", name="get_team")
     * @Method("GET")
     */
    public function getTeamAction($id)
    {
        $team = $this->getDoctrine()
            ->getRepository('AppBundle:Team')
            ->findOneById($id);

        if (!$team) {
            throw $this->createNotFoundException(sprintf(
                'No team found with id %s',
                $id
            ));
        }

        return $this->createApiResponse($team);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @Route("/api/teams/{id}", name="put_team")
     * @Method("PUT")
     */
    public function updateAction($id, Request $request)
    {
        $team = $this->getDoctrine()
            ->getRepository('AppBundle:Team')
            ->findOneById($id);

        if (!$team) {
            throw $this->createNotFoundException(sprintf(
                'No team found with id %s',
                $id
            ));
        }

        $form = $this->createForm('AppBundle\Form\UpdateTeamType', $team);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($team);
        $em->flush();

        return $this->createApiResponse($team);
    }
}
