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

        /*
        $gameUrl = $this->generateUrl(
            'get_round',
            ['id' => $game->getId()]
        );
        */
        $gameUrl = '/api/games/1';

        // Response handling
        $response = $this->createApiResponse($game, Response::HTTP_CREATED);
        $response->headers->set('Location', $gameUrl);

        return $response;
    }
}
