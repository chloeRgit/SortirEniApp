<?php

namespace App\Repository;

use App\Entity\Vile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vile[]    findAll()
 * @method Vile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vile::class);
    }

    // /**
    //  * @return Vile[] Returns an array of Vile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vile
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
