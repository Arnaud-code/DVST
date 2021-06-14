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

    public function getSummaryByUser(User $user)
    {
        return $this->createQueryBuilder('pr')
            ->select('IDENTITY(pr.tire) as tire_id')
            ->addSelect('tr.name as tire')
            // ->addSelect("(" . $tr->getTires()->findBy('t')
            //     ->select('t')
            //     ->where('t = pr.tire') . ") AS tire1")
            ->addSelect('IDENTITY(pr.driver) as driver_id')
            ->addSelect('dr.name as driver')
            ->addSelect('IDENTITY(pr.circuit) as circuit_id')
            ->addSelect('cr.name as circuit')
            ->addSelect("(" . $this->createQueryBuilder('pr2')
                ->select('count(pr2)')
                ->where('pr2.user = pr.user')
                ->andWhere('pr2.tire = pr.tire')
                ->andWhere('pr2.driver = pr.driver')
                ->andWhere('pr2.circuit = pr.circuit') . ") AS counter")
            ->where('pr.user = :user')
            ->setParameter('user', $user)
            // ->innerJoin($tr->createQueryBuilder('tr')->select())
            ->innerJoin('App\Entity\Tire', 'tr', 'WITH', 'IDENTITY(pr.tire) = tr.id')
            ->innerJoin('App\Entity\Driver', 'dr', 'WITH', 'IDENTITY(pr.driver) = dr.id')
            ->innerJoin('App\Entity\Circuit', 'cr', 'WITH', 'IDENTITY(pr.circuit) = cr.id')
            ->orderBy('pr.tire', 'ASC')
            ->addOrderBy('pr.driver', 'ASC')
            ->addOrderBy('pr.circuit', 'ASC')
            ->distinct()
            ->getQuery()
            // ->getSql();
            ->getResult();
    }
}
