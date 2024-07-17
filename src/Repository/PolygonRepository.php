<?php

namespace App\Repository;

use App\Entity\Polygon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Polygon>
 *
 * @method Polygon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Polygon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Polygon[]    findAll()
 * @method Polygon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PolygonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Polygon::class);
    }

//    /**
//     * @return Polygon[] Returns an array of Polygon objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Polygon
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
