<?php

namespace App\Repository;

use App\Entity\ProductionMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductionMonthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductionMonth::class);
    }

    // Ajoutez ici des méthodes spécifiques pour interagir avec la vue
    // Exemple : Trouver les entrées pour un mois spécifique
    public function findByMonth(\DateTimeInterface $mois)
    {
        return $this->createQueryBuilder('p')
            ->where('p.mois = :mois')
            ->setParameter('mois', $mois)
            ->getQuery()
            ->getResult();
    }

    
}
