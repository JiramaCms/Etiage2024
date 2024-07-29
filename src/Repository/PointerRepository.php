<?php

namespace App\Repository;

use App\Entity\Pointer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pointer>
 *
 * @method Pointer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pointer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pointer[]    findAll()
 * @method Pointer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pointer::class);
    }

//    /**
//     * @return Pointer[] Returns an array of Pointer objects
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

//    public function findOneBySomeField($value): ?Pointer
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
