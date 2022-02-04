<?php

namespace App\Repository;

use App\Entity\Lieu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lieu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lieu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lieu[]    findAll()
 * @method Lieu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LieuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lieu::class);
    }

    // permet de récupérer les lieux d'une ville donnée
    public function LieuFiltre($ville = null)
    {
        $entityManager = $this->getEntityManager();
        $dql = "SELECT l FROM App\Entity\Lieu l";
        if ($ville) {
            $dql .= " WHERE l.ville=:ville";
        }


        $query = $entityManager->createQuery($dql);
        if ($ville) {
            $query->setParameter("ville", $ville);
        }

        return $query->getResult();
    }

}
