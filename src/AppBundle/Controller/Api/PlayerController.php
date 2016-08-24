<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Player;
use AppBundle\Form\PlayerType;

class PlayerController extends Controller
{
    /**
     * @Route("/players", name="create_player")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        // Input validation and handling
        $player = new Player();
        $form = $this->createForm('AppBundle\Form\PlayerType', $player);
        $form->submit(json_decode($request->getContent(), true));

        // New entity persistence
        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();

        // Response handling
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->headers->set('Location', '/players/' . $player->getId());
        return $response;
    }

    /**
     * @Route("/players", name="list_players")
     * @Method("GET")
     */
    public function getPlayers(Request $request)
    {
        return new Response('Will list all players');
    }

    /**
     * @Route("/players/{id}", name="list_player")
     * @Method("GET")
     */
    public function getPlayer($id)
    {
        $player = $this->getDoctrine()
            ->getRepository('AppBundle:Player')
            ->findOneById($id);

        if (!$player) {
            throw $this->createNotFoundException(sprintf(
                'No player found with id "%s"',
                $id
            ));
        }

        $data = array(
            'id' => $player->getId(),
            'name' => $player->getName()
        );

        $response = new Response(json_encode($data), Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/players/{id}", name="update_player")
     * @Method("PUT")
     */
    public function updatePlayerById(Request $request)
    {
        return new Response('Will update a player');
    }

    /**
     * @Route("/players/{id}", name="delete_player")
     * @Method("DELETE")
     */
    public function deletePlayerById(Request $request)
    {
        return new Response('Will delete a player');
    }
}
