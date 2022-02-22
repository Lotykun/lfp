<?php


namespace App\Service;

use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;

class TrainerManager
{
    private $em;
    private $repository;

    /**
     * TrainerManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Trainer::class);
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
     * @return Trainer
     */
    public function create()
    {
        return new Trainer();
    }

    /**
     * @param Trainer $trainer
     * @param bool $flush
     * @return Trainer
     */
    public function save(Trainer $trainer, $flush = true)
    {
        $this->em->persist($trainer);

        if ($flush) {
            $this->em->flush($trainer);
        }

        return $trainer;
    }

    public function remove(Trainer $trainer)
    {
        $this->em->remove($trainer);
        $this->em->flush();
    }
}