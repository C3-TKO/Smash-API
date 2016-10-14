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
     * @Route("/api/teams/{id}/name", name="put_team_name")
     * @Method("PUT")
     */
    function updateTeamNameAction($id, Request $request)
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

        $data = json_decode($request->getContent(), true);

        $team->setName($data['name']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($team);
        $em->flush();

        return $this->createApiResponse($team);
    }
}
