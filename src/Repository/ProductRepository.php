<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $sr;

    public function __construct(ManagerRegistry $registry, SubscriptionRepository $sr)
    {
        parent::__construct($registry, Product::class);
        $this->sr = $sr;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getProductsByUser(User $user)
    {
        // return $this->createQueryBuilder('pr1')
        //     ->select('IDENTITY(pr1.tire) as tire_id')
        //     ->addSelect('tr.name as tire')
        //     ->addSelect('IDENTITY(pr1.driver) as driver_id')
        //     ->addSelect('dr.name as driver')
        //     ->addSelect('IDENTITY(pr1.circuit) as circuit_id')
        //     ->addSelect('cr.name as circuit')
        //     ->addSelect("(" . $this->createQueryBuilder('pr2')
        //         ->select('count(pr2)')
        //         ->where('pr2.user = pr1.user')
        //         ->andWhere('pr2.tire = pr1.tire')
        //         ->andWhere('pr2.driver = pr1.driver')
        //         ->andWhere('pr2.circuit = pr1.circuit') . ") AS counter")
        //     ->where('pr1.user = :user')
        //     ->setParameter('user', $user)
        //     ->innerJoin('App\Entity\Tire', 'tr', 'WITH', 'IDENTITY(pr1.tire) = tr.id')
        //     ->innerJoin('App\Entity\Driver', 'dr', 'WITH', 'IDENTITY(pr1.driver) = dr.id')
        //     ->innerJoin('App\Entity\Circuit', 'cr', 'WITH', 'IDENTITY(pr1.circuit) = cr.id')
        //     ->orderBy('pr1.tire', 'ASC')
        //     ->addOrderBy('pr1.driver', 'ASC')
        //     ->addOrderBy('pr1.circuit', 'ASC')
        //     ->distinct()
        //     ->getQuery()
        //     ->getResult();

        // return $this->sr->createQueryBuilder('s')
        //     ->select('s.id as subscription')
        //     ->addSelect('IDENTITY(s.product) as product')
        //     ->addSelect('IDENTITY(s.user) as user')
        //     ->andWhere('IDENTITY(s.user) = :user')
        //     ->setParameter('user', $user->getId())
        //     ->getDQL();

        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->addSelect('IDENTITY(p.category)')
            ->addSelect('p.name')
            ->addSelect('p.slug')
            ->addSelect('p.sort')
            ->addSelect('p.icon')
            ->addSelect('p.access')
            ->addSelect('sub.subscription')
            // ->leftJoin('App\Entity\Subscription', 's', Join::ON, 'p.id = IDENTITY(s.product)')
            ->leftJoin(
                ($this->sr->createQueryBuilder('s')
                    ->select('s.id as subscription')
                    ->addSelect('IDENTITY(s.product) as product')
                    ->addSelect('IDENTITY(s.user) as user')
                    ->andWhere('s.user = :user')
                    ->setParameter('user', $user)),
                'sub',
                'ON',
                'p.id = sub.product'
            )
            // ->andWhere()
            // ->getDQL();
            ->getQuery()
            ->getResult();
    }
}
