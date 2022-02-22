<?php


namespace App\Service;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;

class PlayerManager
{
    private $em;
    private $repository;

    /**
     * PlayerManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Player::class);
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
     * @return Player
     */
    public function create()
    {
        return new Player();
    }

    /**
     * @param Player $player
     * @param bool $flush
     * @return Player
     */
    public function save(Player $player, $flush = true)
    {
        $this->em->persist($player);

        if ($flush) {
            $this->em->flush($player);
        }

        return $player;
    }

    public function remove(Player $player)
    {
        $this->em->remove($player);
        $this->em->flush();
    }
}