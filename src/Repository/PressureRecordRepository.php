<?php

namespace App\Repository;

use App\Entity\PressureRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PressureRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method PressureRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method PressureRecord[]    findAll()
 * @method PressureRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PressureRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PressureRecord::class);
    }

    // /**
    //  * @return PressureRecord[] Returns an array of PressureRecord objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PressureRecord
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
