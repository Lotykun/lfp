<?php

namespace App\Object;


use App\Entity\Player;
use App\Entity\Team;
use JMS\Serializer\Annotation\Type;


class ApiListTeamPlayersResponse
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
     * @var array
     */
    private $players;


    public function __construct(Team $team, $players = array())
    {
        $this->team = $team;
        foreach ($players as $player){
            $this->addPlayer($player);
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
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param array $players
     */
    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }

    /**
     * @param Player $player
     * @return array
     */
    public function addPlayer(Player $player)
    {
        $this->players[] = $player;
        return $this->players;
    }
}
