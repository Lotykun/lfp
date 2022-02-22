<?php


namespace App\Service;

use App\Entity\Contract;
use App\Entity\EntityBase;
use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;

class TeamManager
{
    private $em;
    private $repository;
    /** @var ContractManager */
    private $contractManager;

    /**
     * TeamManager constructor.
     * @param EntityManagerInterface $em
     * @param ContractManager $contractManager
     */
    public function __construct(EntityManagerInterface $em, ContractManager $contractManager)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Team::class);
        $this->contractManager = $contractManager;
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $repository
     */
    public function setRepository($repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return Team
     */
    public function create()
    {
        return new Team();
    }

    /**
     * @param Team $team
     * @param bool $flush
     * @return Team
     */
    public function save(Team $team, $flush = true)
    {
        $this->em->persist($team);

        if ($flush) {
            $this->em->flush($team);
        }

        return $team;
    }

    /**
     * @param Team $team
     */
    public function remove(Team $team)
    {
        $this->em->remove($team);
        $this->em->flush();
    }

    public function getTeamPlayers(Team $team)
    {
        return $players = $this->contractManager->getRepository()->findActivePlayersByTeam($team);
    }

    /**
     * @param Team $team
     * @return float|null
     */
    public function getTeamCurrentBudget(Team $team)
    {
        $criteria = array(
            "team" => $team,
            "active" => true,
        );
        $contracts = $this->contractManager->getRepository()->findBy($criteria);
        $amount = 0.0;
        /** @var Contract $contract */
        foreach ($contracts as $contract){
            $amount += $contract->getAmount();
        }
        return $amount;
    }

    public function existsPlayerInTeam(Team $team, Player $player)
    {
        $criteria = array(
            "team" => $team,
            "player" => $player,
            "active" => true,
        );
        return $this->contractManager->getRepository()->findOneBy($criteria);
    }

    /**
     * @param Team $team
     * @param Player $player
     * @param $amount
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @return Contract
     * @throws \Exception
     */
    public function addPlayerInTeam(Team $team, Player $player, $amount, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        $now = new \DateTime();
        if ($now->format('m') > '06'){
            $year = intval($now->format('Y')) + 1;
            $endContractDate = new \DateTime($year . '-06-30');
        } else {
            $year = intval($now->format('Y'));
            $endContractDate = new \DateTime($year . '-06-30');
        }

        $contract = $this->contractManager->create();
        $contract->setTeam($team);
        $contract->setPlayer($player);
        $contract->setAmount($amount);
        $contract->setActive(true);
        if (isset($startDate) && !empty($startDate)){
            $contract->setStartDate($startDate);
        } else {
            $contract->setStartDate(new \DateTime());
        }

        if (isset($endDate) && !empty($endDate)){
            $contract->setEndDate($endDate);
        } else {
            $contract->setEndDate($endContractDate);
        }

        $contract = $this->contractManager->save($contract);

        return $contract;
    }

    /**
     * @param Team $team
     * @param Trainer $trainer
     * @return mixed
     */
    public function existsTrainerInTeam(Team $team, Trainer $trainer)
    {
        $criteria = array(
            "team" => $team,
            "trainer" => $trainer,
            "active" => true,
        );
        return $this->contractManager->getRepository()->findOneBy($criteria);
    }

    /**
     * @param Team $team
     * @param Trainer $trainer
     * @param $amount
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @return Contract
     * @throws \Exception
     */
    public function addTrainerInTeam(Team $team, Trainer $trainer, $amount, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        $now = new \DateTime();
        if ($now->format('m') > '06'){
            $year = intval($now->format('Y')) + 1;
            $endContractDate = new \DateTime($year . '-06-30');
        } else {
            $year = intval($now->format('Y'));
            $endContractDate = new \DateTime($year . '-06-30');
        }

        $contract = $this->contractManager->create();
        $contract->setTeam($team);
        $contract->setTrainer($trainer);
        $contract->setAmount($amount);
        $contract->setActive(true);
        if (isset($startDate) && !empty($startDate)){
            $contract->setStartDate($startDate);
        } else {
            $contract->setStartDate(new \DateTime());
        }

        if (isset($endDate) && !empty($endDate)){
            $contract->setEndDate($endDate);
        } else {
            $contract->setEndDate($endContractDate);
        }

        $contract = $this->contractManager->save($contract);

        return $contract;
    }
}