<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Team;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use AppBundle\Entity\Player;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PlayerController extends BaseController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     * @Route("/api/players", name="post_player")
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
        $em->getConnection()->beginTransaction();

        // Try and make the transaction
        try {
            $em->persist($player);
            $em->flush();

            $this->createTeamsForNewPlayer($em, $player);


            // Try and commit the transaction
            $em->getConnection()->commit();
        } catch (Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            throw $e;
        }

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
     * @Route("/api/players", name="get_players")
     * @Method("GET")
     */
    public function getCollectionAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Player')
            ->findAllQueryBuilder($filter);

        $paginatedCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'get_players');

        $response = $this->createApiResponse($paginatedCollection);
        return $response;
    }

    /**
     * @Route("/api/players/{id}", name="get_player")
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
     * @Security("is_granted('ROLE_USER')")
     * @Route("/api/players/{id}", name="put_player")
     * @Method("PUT")
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
     * @Security("is_granted('ROLE_USER')")
     * @Route("/api/players/{id}", name="delete_player")
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
     * Creates all possible team combination with the new player
     *
     * @param EntityManager $em
     * @param Player $newPlayer
     */
    private function createTeamsForNewPlayer(EntityManager $em, Player $newPlayer)
    {
        $players = $em->getRepository('AppBundle:Player')->findAll();

        foreach($players as $existingPlayer) {
            if($existingPlayer->getId() === $newPlayer->getId())
            {
                continue;
            }

            $team = new Team();
            $team->setPlayerA($existingPlayer);
            $team->setPlayerB($newPlayer);
            $em->persist($team);
        }

        $em->flush();
    }
}
