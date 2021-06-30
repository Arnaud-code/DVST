<?php

namespace App\Repository;

use App\Entity\PressureRecord;
use App\Entity\User;
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

    public function getCombinationsByUser(User $user)
    {
        return $this->createQueryBuilder('pr1')
            ->select('IDENTITY(pr1.tire) as tire_id')
            ->addSelect('tr.name as tire')
            ->addSelect('IDENTITY(pr1.driver) as driver_id')
            ->addSelect('dr.name as driver')
            ->addSelect('IDENTITY(pr1.circuit) as circuit_id')
            ->addSelect('cr.name as circuit')
            ->addSelect("(" . $this->createQueryBuilder('pr2')
                ->select('count(pr2)')
                ->where('pr2.user = pr1.user')
                ->andWhere('pr2.tire = pr1.tire')
                ->andWhere('pr2.driver = pr1.driver')
                ->andWhere('pr2.circuit = pr1.circuit') . ") AS counter")
            ->where('pr1.user = :user')
            ->setParameter('user', $user)
            ->innerJoin('App\Entity\Tire', 'tr', 'WITH', 'IDENTITY(pr1.tire) = tr.id')
            ->innerJoin('App\Entity\Driver', 'dr', 'WITH', 'IDENTITY(pr1.driver) = dr.id')
            ->innerJoin('App\Entity\Circuit', 'cr', 'WITH', 'IDENTITY(pr1.circuit) = cr.id')
            ->orderBy('pr1.tire', 'ASC')
            ->addOrderBy('pr1.driver', 'ASC')
            ->addOrderBy('pr1.circuit', 'ASC')
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}
