<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contract|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contract|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contract[]    findAll()
 * @method Contract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    public function findActivePlayersContractsByTeam(Team $team, $params = array())
    {
        $qb = $this->createQueryBuilder('c');
        $qb->join('c.player', 'p');
        $qb->andWhere('c.team = :val')->setParameter('val', $team);
        $qb->andWhere('c.player IS NOT NULL');
        $qb->andWhere('c.active = :active')->setParameter('active', true);
        if (array_key_exists('name', $params) && $params['name'] !== null) {
            $qb->andWhere('p.name = :name')->setParameter('name', $params['name']);
        }
        if (array_key_exists('page', $params) && $params['page'] !== null) {
            $qb->setFirstResult(intval($params['page']) * intval($params['maxResults']));
            $qb->setMaxResults($params['maxResults']);
        }

        return $qb->getQuery()->getResult();
    }

    public function findActiveTrainerByTeam(Team $team)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.team = :val')->setParameter('val', $team);
        $qb->andWhere('c.trainer IS NOT NULL');
        $qb->andWhere('c.active = :active')->setParameter('active', true);
        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Contract[] Returns an array of Contract objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contract
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
