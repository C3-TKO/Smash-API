<?php

namespace AppBundle\Controller\Api;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use AppBundle\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class GameController extends BaseController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     * @Route("/api/games", name="post_game")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        // Input validation and handling
        $game = new Game();
        $form = $this->createForm('AppBundle\Form\GameType', $game);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        // New entity persistence
        $em = $this->getDoctrine()->getManager();
        $em->persist($game);
        $em->flush();


        $gameUrl = $this->generateUrl(
            'get_game',
            ['id' => $game->getId()]
        );

        // Response handling
        $response = $this->createApiResponse($game, Response::HTTP_CREATED);
        $response->headers->set('Location', $gameUrl);

        return $response;
    }

    /**
     * @Route("/api/games", name="get_games")
     * @Method("GET")
     */
    public function getCollectionAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->findAllQueryBuilder($filter);

        $paginatedCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'get_games');

        $response = $this->createApiResponse($paginatedCollection);
        return $response;
    }

    /**
     * @Route("/api/games/{id}", name="get_game")
     * @Method("GET")
     */
    public function getAction($id)
    {
        $game = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->findOneById($id);

        if (!$game) {
            throw $this->createNotFoundException(sprintf(
                'No player found with id %s',
                $id
            ));
        }

        return $this->createApiResponse($game);
    }
}
