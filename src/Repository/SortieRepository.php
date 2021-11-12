<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }
    public function sortieFiltree($site=null,$nom=null,$datemin=null,$datemax=null,$organisateur=null,$passees=null)
    {
        $entityManager=$this->getEntityManager();
        $dql="SELECT s FROM App\Entity\Sortie s  WHERE s.site is not null";

        if($site){
            $dql.=" AND s.site=:site";
        }
        if($organisateur){
            $dql.=" AND s.organisateur=:organisateur";
        }
        if($datemin){
            $dql.=" AND s.dateHeureDebut>=:datemin";
        }
        if($datemax){
            $dql.=" AND s.dateHeureDebut<=:datemax";
        }
        if($nom){
            $dql.=" AND s.nom LIKE :nom";
        }
        if($passees){
            $dql.=" AND s.dateHeureDebut<:passees";
        }

        $query=$entityManager->createQuery($dql);
        if($site){
            $query->setParameter("site",$site);
        }
        if($organisateur){
            $query->setParameter("organisateur",$organisateur);
        }
        if($datemin){
            $query->setParameter("datemin",$datemin);
        }
        if($datemax){
           $query->setParameter("datemax",$datemax);
        }
        if($nom){
            $query->setParameter("nom",$nom);
        }
        if($passees){
            $query->setParameter("passees",$passees);
        }


        return $query->getResult();
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
