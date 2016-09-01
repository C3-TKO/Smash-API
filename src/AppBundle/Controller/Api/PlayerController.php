<?php

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use AppBundle\Entity\Player;

class PlayerController extends BaseController
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

        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        // New entity persistence
        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();

        $playerUrl = $this->generateUrl(
            'get_player',
            ['id' => $player->getId()]
        );

        // Response handling
        $response = $this->createApiResponse($player, Response::HTTP_CREATED);
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

        return $this->createApiResponse(['players' => $players]);
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
                'No player found with id %s',
                $id
            ));
        }

        return $this->createApiResponse($player);
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
                'No player found with id %s',
                $id
            ));
        }

        $form = $this->createForm('AppBundle\Form\UpdatePlayerType', $player);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();

        return $this->createApiResponse($player);
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

    /**
     * @param Request $request
     * @param Form $form
     */
    private function processForm(Request $request, Form $form)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);

            throw new ApiProblemException($apiProblem);
        }

        $clearMissing = $request->getMethod() !== 'PATCH';
        $form->submit($data, $clearMissing);
    }
}
