<?php

namespace App\Repository;

use App\Entity\Besoin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Besoin>
 *
 * @method Besoin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Besoin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Besoin[]    findAll()
 * @method Besoin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BesoinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Besoin::class);
    }

    public function findBesoinForSiteAndDate($siteId, \DateTimeInterface $dateProduction)
    {
        return $this->createQueryBuilder('b')
            ->where('b.site = :siteId')
            ->andWhere('b.dateDebut <= :dateProduction')
            ->andWhere('(b.dateFin >= :dateProduction OR b.dateFin IS NULL)')
            ->setParameter('siteId', $siteId)
            ->setParameter('dateProduction', $dateProduction)
            ->getQuery()
            ->getOneOrNullResult(); // Retourne un seul résultat ou null
    }
}
