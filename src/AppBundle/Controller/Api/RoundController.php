<?php

namespace AppBundle\Controller\Api;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use AppBundle\Entity\Round;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RoundController extends BaseController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     * @Route("/api/rounds", name="post_round")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        // Input validation and handling
        $round = new Round();
        $form = $this->createForm('AppBundle\Form\RoundType', $round);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        // New entity persistence
        $em = $this->getDoctrine()->getManager();
        $em->persist($round);
        $em->flush();

        /*
        $roundUrl = $this->generateUrl(
            'get_round',
            ['id' => $round->getId()]
        );
        */
        $roundUrl = '/api/rounds/' . $round->getId();

        // Response handling
        $response = $this->createApiResponse($round, Response::HTTP_CREATED);
        $response->headers->set('Location', $roundUrl);

        return $response;
    }


    /**
     * @Route("/api/rounds", name="get_rounds")
     * @Method("GET")
     */
    public function getCollectionAtion(Request $request) {
        $filter = $request->query->get('filter');

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Round')
            ->findAllQueryBuilder($filter);

        $paginatedCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'get_rounds');

        $response = $this->createApiResponse($paginatedCollection);
        return $response;
    }
}
