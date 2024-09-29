<?php

namespace App\Repository;

use App\Entity\StationProductionMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use DateTimeInterface;

/**
 * @extends ServiceEntityRepository<StationProductionMonth>
 *
 * @method StationProductionMonth|null find($id, $lockMode = null, $lockVersion = null)
 * @method StationProductionMonth|null findOneBy(array $criteria, array $orderBy = null)
 * @method StationProductionMonth[]    findAll()
 * @method StationProductionMonth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationProductionMonthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StationProductionMonth::class);
    }

    
}