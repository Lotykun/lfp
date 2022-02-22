<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Trainer;
use App\Object\ApiErrorResponse;
use App\Service\PlayerManager;
use App\Service\TeamManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TeamType;
use App\Entity\Team;

/**
 * Team controller.
 * @Route("/api", name="api_")
 */
class TeamController extends AbstractFOSRestController
{
    /**
     * Lists all Teams.
     * @Rest\Get("/teams")
     *
     * @param TeamManager $teamManager
     * @return Response
     */
    public function getTeamsAction(TeamManager $teamManager): Response
    {
        try {
            $teams = $teamManager->getRepository()->findAll();
            return $this->handleView($this->view($teams));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * List Team.
     * @Rest\Get("/team/{id}")
     *
     * @param TeamManager $teamManager
     * @param Team $team
     * @return Response
     */
    public function getTeamAction(TeamManager $teamManager, Team $team): Response
    {
        try {
            return $this->handleView($this->view($team));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Create team.
     * @Rest\Post("/team")
     * @Rest\Post("/team/{id}", defaults={"id" = null})
     *
     * @param Request $request
     * @param TeamManager $teamManager
     * @param Team|null $team
     * @return Response
     */
    public function postTeamAction(Request $request, TeamManager $teamManager, Team $team = null): Response
    {
        try {
            if (!$team){
                $team = $teamManager->create();
            }
            $form = $this->createForm(TeamType::class, $team);
            $data = json_decode($request->getContent(), true);
            $form->submit($data);
            if ($form->isSubmitted() && $form->isValid()) {
                $exist = $teamManager->getRepository()->findOneBy(['name' => $data['name']]);
                if (!$exist){
                    $teamManager->save($team);
                    return $this->handleView($this->view($team, Response::HTTP_CREATED));
                }
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Add player to Team
     * @Rest\Post("/team/add-player/{team}/{player}")
     *
     * @param Request $request
     * @param TeamManager $teamManager
     * @param Team $team
     * @param Player $player
     * @return Response
     */
    public function postPlayerInTeamAction(Request $request, TeamManager $teamManager, Team $team, Player $player): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $currentBudgetTeam = $teamManager->getTeamCurrentBudget($team);
            $amountToSpend = $team->getBudget() - $currentBudgetTeam;
            if ($data['amount'] <= $amountToSpend){
                $exist = $teamManager->existsPlayerInTeam($team, $player);
                if (!$exist){
                    $contract = $teamManager->addPlayerInTeam($team, $player, $data['amount']);
                    //NOTIFICATIFICATION FAlta

                    return $this->handleView($this->view($contract, Response::HTTP_CREATED));
                }
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Add trainer to Team
     * @Rest\Post("/team/add-trainer/{team}/{trainer}")
     *
     * @param Request $request
     * @param TeamManager $teamManager
     * @param Team $team
     * @param Trainer $trainer
     * @return Response
     */
    public function postTrainerInTeamAction(Request $request, TeamManager $teamManager, Team $team, Trainer $trainer): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $currentBudgetTeam = $teamManager->getTeamCurrentBudget($team);
            $amountToSpend = $team->getBudget() - $currentBudgetTeam;
            if ($data['amount'] <= $amountToSpend){
                $exist = $teamManager->existsTrainerInTeam($team, $trainer);
                if (!$exist){
                    $contract = $teamManager->addTrainerInTeam($team, $trainer, $data['amount']);
                    //NOTIFICATIFICATION FAlta

                    return $this->handleView($this->view($contract, Response::HTTP_CREATED));
                }
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
