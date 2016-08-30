<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use AppBundle\Entity\Player;

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
        $this->processForm($request, $form);

        // New entity persistence
        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();

        $playerUrl = $this->generateUrl(
            'get_player',
            ['id' => $player->getId()]
        );

        // Response handling
        $json = $this->serialize($player);
        $response = new Response($json, Response::HTTP_CREATED);
        $response->headers->set('Location', $playerUrl);

        return $response;
    }

    /**
     * @Route("/players", name="get_players")
     * @Method("GET")
     */
    public function getCollectionAction()
    {
        $players = $this->getDoctrine()
            ->getRepository('AppBundle:Player')
            ->findAll();

        $json = $this->serialize(['players' => $players]);
        $response = new Response($json, Response::HTTP_OK);

        return $response;
    }

    /**
     * @Route("/players/{id}", name="get_player")
     * @Method("GET")
     */
    public function getAction($id)
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

        $json = $this->serialize($player);

        $response = new Response($json, Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/players/{id}", name="put_player")
     * @Method({"PUT", "PATCH"})
     */
    public function updateAction($id, Request $request)
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

        $form = $this->createForm('AppBundle\Form\UpdatePlayerType', $player);
        $this->processForm($request, $form);

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();
        $json = $this->serialize($player);
        $response = new Response($json, Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/players/{id}", name="delete_player")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $player = $this->getDoctrine()
            ->getRepository('AppBundle:Player')
            ->findOneById($id);

        if ($player) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($player);
            $em->flush();
        }

        // Will always return a HTTP 204 as it doesn't matter if the player existed or not. What matters is idempotancy!
        // Requesting an idempotant endpoint prescribes that the repsonse must always be the same
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    private function serialize($data)
    {
        return $this->container->get('jms_serializer')
            ->serialize($data, 'json');
    }

    /**
     * @param Request $request
     * @param Form $form
     */
    private function processForm(Request $request, Form $form)
    {
        $data = json_decode($request->getContent(), true);

        $clearMissing = $request->getMethod() !== 'PATCH';
        $form->submit($data, $clearMissing);
    }
}
