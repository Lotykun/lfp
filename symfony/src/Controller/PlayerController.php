<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Player;
use App\Form\PlayerType;

/**
 * Player controller.
 * @Route("/api", name="api_")
 */
class PlayerController extends AbstractFOSRestController
{
    /**
     * Lists all Players.
     * @Rest\Get("/players")
     *
     * @return Response
     */
    public function getPlayersAction(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Player::class);
        $players = $repository->findAll();
        return $this->handleView($this->view($players));
    }

    /**
     * Create Player.
     * @Rest\Post("/player")
     * @param Request $request
     *
     * @return Response
     */
    public function postPlayerAction(Request $request): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }

    }
}
