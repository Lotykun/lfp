<?php

namespace App\Controller;

use App\Object\ApiErrorResponse;
use App\Service\PlayerManager;
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
     * @param PlayerManager $playerManager
     * @return Response
     */
    public function getPlayersAction(PlayerManager $playerManager): Response
    {
        try {
            $players = $playerManager->getRepository()->findAll();
            return $this->handleView($this->view($players));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * List Player.
     * @Rest\Get("/player/{id}")
     *
     * @param Player $player
     * @return Response
     */
    public function getPlayerAction(Player $player): Response
    {
        try {
            return $this->handleView($this->view($player));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Create Player.
     * @Rest\Post("/player")
     * @Rest\Post("/player/{id}", defaults={"id" = null})
     *
     * @param Request $request
     * @param PlayerManager $playerManager
     * @param Player|null $player
     * @return Response
     */
    public function postPlayerAction(Request $request, PlayerManager $playerManager, Player $player = null): Response
    {
        try {
            $mode = 'update';
            if (!$player){
                $player = $playerManager->create();
                $mode = 'create';
            }
            $form = $this->createForm(PlayerType::class, $player);
            $data = json_decode($request->getContent(), true);
            $form->submit($data);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($mode == 'create'){
                    $exist = $playerManager->getRepository()->findOneby(array('name' => $player->getName()));
                    if ($exist){
                        $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Player: ' . $player->getName() .' is already created with this name');
                        return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
                    }
                }
                $playerManager->save($player);
                return $this->handleView($this->view($player, Response::HTTP_CREATED));
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
