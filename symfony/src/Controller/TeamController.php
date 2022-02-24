<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Trainer;
use App\Object\ApiErrorResponse;
use App\Object\ApiListTeamPlayersResponse;
use App\Object\ApiListTeamSquadResponse;
use App\Object\ApiListTeamTrainerResponse;
use App\Service\Notification\NotificationService;
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
     * @Rest\Get("/teams", name="list_teams")
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
     * @Rest\Get("/team/{id}", name="list_team", requirements={"id":"\d+"})
     *
     * @param Team $team
     * @return Response
     */
    public function getTeamAction(Team $team): Response
    {
        try {
            return $this->handleView($this->view($team));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * * List Team players.
     * @Rest\Get("/team/players/{id}", name="list_team_players", requirements={"id":"\d+"})
     *
     * @param Request $request
     * @param TeamManager $teamManager
     * @param Team $team
     * @return Response
     */
    public function getTeamPlayersAction(Request $request, TeamManager $teamManager, Team $team): Response
    {
        try {
            $params = $request->query->all();
            if (isset($params['page'])) {
                if (intval($params['page']) == 1){
                    $params['page'] = 0;
                } else {
                    $params['page'] = intval($params['page'] - 1);
                }

            }
            $params['maxResults'] = 2;
            $players = $teamManager->getTeamActivePlayers($team, $params);
            $response = new ApiListTeamPlayersResponse($team, $players);
            return $this->handleView($this->view($response));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * * List Team Trainer.
     * @Rest\Get("/team/trainer/{id}", name="list_team_trainer", requirements={"id":"\d+"})
     *
     * @param Request $request
     * @param TeamManager $teamManager
     * @param Team $team
     * @return Response
     */
    public function getTeamTrainerAction(Request $request, TeamManager $teamManager, Team $team): Response
    {
        try {
            $trainer = $teamManager->getTeamActiveTrainer($team);
            $response = new ApiListTeamTrainerResponse($team, $trainer);
            return $this->handleView($this->view($response));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * * List Team Trainer.
     * @Rest\Get("/team/squad/{id}", name="list_team_squad", requirements={"id":"\d+"})
     *
     * @param Request $request
     * @param TeamManager $teamManager
     * @param Team $team
     * @return Response
     */
    public function getTeamSquadAction(Request $request, TeamManager $teamManager, Team $team): Response
    {
        try {
            $params = $request->query->all();
            if (isset($params['page'])) {
                if (intval($params['page']) == 1){
                    $params['page'] = 0;
                } else {
                    $params['page'] = intval($params['page'] - 1);
                }

            }
            $params['maxResults'] = 2;
            $players = $teamManager->getTeamActivePlayers($team, $params);
            $trainer = $teamManager->getTeamActiveTrainer($team);
            $response = new ApiListTeamSquadResponse($team, $players, $trainer);
            return $this->handleView($this->view($response));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Create team.
     * @Rest\Post("/team")
     * @Rest\Post("/team/{id}", name="team_create", defaults={"id" = null})
     *
     * @param Request $request
     * @param TeamManager $teamManager
     * @param Team|null $team
     * @return Response
     */
    public function postTeamAction(Request $request, TeamManager $teamManager, Team $team = null): Response
    {
        try {
            $mode = 'update';
            if (!$team){
                $team = $teamManager->create();
                $mode = 'create';
            }
            $form = $this->createForm(TeamType::class, $team);
            $data = json_decode($request->getContent(), true);
            $form->submit($data);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($mode == 'create'){
                    $exist = $teamManager->getRepository()->findOneby(array('name' => $team->getName()));
                    if ($exist){
                        $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Team: ' . $team->getName() .' is already created with this name');
                        return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
                    }
                }
                $teamManager->save($team);
                return $this->handleView($this->view($team, Response::HTTP_CREATED));
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Add player to Team
     * @Rest\Post("/team/add-player/{team}/{player}", name="team_player_create", requirements={"team":"\d+", "player":"\d+"})
     *
     * @param Request $request
     * @param NotificationService $ns
     * @param TeamManager $teamManager
     * @param Team $team
     * @param Player $player
     * @return Response
     */
    public function postPlayerInTeamAction(Request $request, NotificationService $ns, TeamManager $teamManager, Team $team, Player $player): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $currentBudgetTeam = $teamManager->getTeamCurrentBudget($team);
            $amountToSpend = $team->getBudget() - $currentBudgetTeam;
            if ($data['amount'] <= $amountToSpend){
                $exist = $teamManager->existsPlayerInTeam($team, $player);
                if (!$exist){
                    $exist = $teamManager->existsPlayerInOtherTeams($player);
                    if (!$exist){
                        $contract = $teamManager->addPlayerInTeam($team, $player, $data['amount']);
                        $ns->addPlayerToTeamNotification($team, $player);
                        return $this->handleView($this->view($contract, Response::HTTP_CREATED));
                    } else {
                        $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Player: ' . $player->getName() . ' has already an active contract with another team');
                        return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
                    }
                } else {
                    $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Team: ' . $team->getName() . ' has already this player active, Player: ' . $player->getName());
                    return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
                }
            } else {
                $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR,
                    'Team: ' . $team->getName() . ' has not enough money to afford this transaction, 
                    TeamBudget: ' . number_format((float)$team->getBudget(), 2, '.', '') . ' € 
                    CurrentTeamBudget: ' . number_format((float)$currentBudgetTeam, 2, '.', '') . ' €
                    PlayerSalary: ' . number_format((float)$data['amount'], 2, '.', '') . ' €');
                return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Remove player from Team
     * @Rest\Post("/team/remove-player/{team}/{player}", name="team_player_remove", requirements={"team":"\d+", "player":"\d+"})
     *
     * @param Request $request
     * @param NotificationService $ns
     * @param TeamManager $teamManager
     * @param Team $team
     * @param Player $player
     * @return Response
     */
    public function postPlayerOutTeamAction(Request $request, NotificationService $ns, TeamManager $teamManager, Team $team, Player $player): Response
    {
        try {
            $exist = $teamManager->existsPlayerInTeam($team, $player);
            if ($exist){
                $contract = $teamManager->removePlayerFromTeam($team, $player);
                $ns->removePlayerFromTeamNotification($team, $player);
                return $this->handleView($this->view($contract, Response::HTTP_OK));
            } else {
                $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Team: ' . $team->getName() . ' has not this player active, Player: ' . $player->getName());
                return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Add trainer to Team
     * @Rest\Post("/team/add-trainer/{team}/{trainer}", name="team_trainer_create", requirements={"team":"\d+", "trainer":"\d+"})
     *
     * @param Request $request
     * @param NotificationService $ns
     * @param TeamManager $teamManager
     * @param Team $team
     * @param Trainer $trainer
     * @return Response
     */
    public function postTrainerInTeamAction(Request $request, NotificationService $ns, TeamManager $teamManager, Team $team, Trainer $trainer): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $currentBudgetTeam = $teamManager->getTeamCurrentBudget($team);
            $amountToSpend = $team->getBudget() - $currentBudgetTeam;
            if ($data['amount'] <= $amountToSpend){
                $exist = $teamManager->existsTrainerInOtherTeams($trainer);
                if (!$exist){
                    $exist = $teamManager->existsTrainerInTeam($team, $trainer);
                    if (!$exist){
                        $contract = $teamManager->addTrainerInTeam($team, $trainer, $data['amount']);
                        $ns->addTrainerToTeamNotification($team, $trainer);
                        return $this->handleView($this->view($contract, Response::HTTP_CREATED));
                    } else {
                        $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Team: ' . $team->getName() . ' has already this trainer active, Trainer: ' . $trainer->getName());
                        return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
                    }
                } else {
                    $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Trainer: ' . $trainer->getName() . ' has already an active contract with another team');
                    return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
                }
            } else {
                $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR,
                    'Team: ' . $team->getName() . ' has not enough money to afford this transaction,
                    TeamBudget: ' . number_format((float)$team->getBudget(), 2, '.', '') . ' €
                    CurrentTeamBudget: ' . number_format((float)$currentBudgetTeam, 2, '.', '') . ' €
                    TrainerSalary: ' . number_format((float)$data['amount'], 2, '.', '') . ' €');
                return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Remove trainer from Team
     * @Rest\Post("/team/remove-trainer/{team}/{trainer}", name="team_trainer_remove", requirements={"team":"\d+", "trainer":"\d+"})
     *
     * @param Request $request
     * @param NotificationService $ns
     * @param TeamManager $teamManager
     * @param Team $team
     * @param Trainer $trainer
     * @return Response
     */
    public function postTrainerOutTeamAction(Request $request, NotificationService $ns, TeamManager $teamManager, Team $team, Trainer $trainer): Response
    {
        try {
            $exist = $teamManager->existsTrainerInTeam($team, $trainer);
            if ($exist){
                $contract = $teamManager->removeTrainerFromTeam($team, $trainer);
                $ns->removeTrainerFromTeamNotification($team, $trainer);
                return $this->handleView($this->view($contract, Response::HTTP_OK));
            } else {
                $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Team: ' . $team->getName() . ' has not this trainer active, Trainer: ' . $trainer->getName());
                return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
