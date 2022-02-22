<?php


namespace App\Service;

use App\Entity\Contract;
use Doctrine\ORM\EntityManagerInterface;

class ContractManager
{
    private $em;
    private $repository;

    /**
     * TeamManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Contract::class);
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
     * @return Contract
     */
    public function create()
    {
        return new Contract();
    }

    /**
     * @param Contract $contract
     * @param bool $flush
     * @return Contract
     */
    public function save(Contract $contract, $flush = true)
    {
        $this->em->persist($contract);

        if ($flush) {
            $this->em->flush($contract);
        }

        return $contract;
    }

    /**
     * @param Contract $contract
     */
    public function remove(Contract $contract)
    {
        $this->em->remove($contract);
        $this->em->flush();
    }
}