<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Player;
use AppBundle\Form\PlayerType;

class PlayerController extends Controller
{
    /**
     * @Route("/players", name="post_player")
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

        $playerUrl = $this->generateUrl(
            'get_player',
            ['id' => $player->getId()]
        );

        // Response handling
        $data = $this->serializePlayer($player);
        $response = new JsonResponse($data, Response::HTTP_CREATED);
        $response->headers->set('Location', $playerUrl);

        return $response;
    }

    /**
     * @Route("/players", name="get_players")
     * @Method("GET")
     */
    public function getPlayers(Request $request)
    {
        $players = $this->getDoctrine()
            ->getRepository('AppBundle:Player')
            ->findAll();

        $data = array('players' => array());
        foreach ($players as $player) {
            $data['players'][] = $this->serializePlayer($player);
        }

        $response = new JsonResponse($data, Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/players/{id}", name="get_player")
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

        $data = $this->serializePlayer($player);

        $response = new JsonResponse($data, Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/players/{id}", name="put_player")
     * @Method("PUT")
     */
    public function updatePlayerById($id, Request $request)
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

        $data = json_decode($request->getContent(), true);
        $form = $this->createForm('AppBundle\Form\PlayerType', $player);
        $form->submit($data);
        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();
        $data = $this->serializePlayer($player);
        $response = new JsonResponse($data, Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/players/{id}", name="delete_player")
     * @Method("DELETE")
     */
    public function deletePlayerById(Request $request)
    {
        return new Response('Will delete a player');
    }

    private function serializePlayer(Player $player)
    {
        return array(
            'id' => $player->getId(),
            'name' => $player->getName()
        );
    }
}
