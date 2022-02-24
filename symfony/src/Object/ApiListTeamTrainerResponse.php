<?php

namespace App\Object;


use App\Entity\Team;
use App\Entity\Trainer;
use JMS\Serializer\Annotation\Type;


class ApiListTeamTrainerResponse
{
    /**
     * Error code
     *
     * @var Team
     */
    private $team;

    /**
     * Error message
     *
     * @var Trainer
     */
    private $trainer;


    public function __construct(Team $team, Trainer $trainer = null)
    {
        $this->team = $team;
        if ($trainer){
            $this->trainer = $trainer;
        }
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    /**
     * @return Trainer
     */
    public function getTrainer(): Trainer
    {
        return $this->trainer;
    }

    /**
     * @param Trainer $trainer
     */
    public function setTrainer(Trainer $trainer): void
    {
        $this->trainer = $trainer;
    }
}
