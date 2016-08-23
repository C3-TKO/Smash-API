<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Player;

class PlayerController extends Controller
{
    /**
     * @Route("/players", name="create_player")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $playerFromRequest = json_decode($request->getContent(), true);
        $player = new Player();
        $player->setName($playerFromRequest['name']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();

        return new Response('Saved player with name: ' . $playerFromRequest['name']);
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
    public function getPlayerById(Request $request)
    {
        return new Response('Will list a player');
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
