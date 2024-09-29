<?php

namespace App\Repository;

use App\Entity\Site;
use App\Entity\Production;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Production>
 *
 * @method Production|null find($id, $lockMode = null, $lockVersion = null)
 * @method Production|null findOneBy(array $criteria, array $orderBy = null)
 * @method Production[]    findAll()
 * @method Production[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Production::class);
    }

    public function findLatestProductionsByStationId(int $stationId, int $limit = 10)
    {   
        $entityManager = $this->getEntityManager();

         // Créez un ResultSetMapping
         $rsm = new ResultSetMappingBuilder($this->getEntityManager());

         // Ajoutez le mapping pour l'entité ZoneVente
         $rsm->addRootEntityFromClassMetadata('App\Entity\Production', 'p');

        $nativeQuery = $entityManager->createNativeQuery('
            SELECT p.*
            FROM production p
            INNER JOIN (
                SELECT station_id, daty, MAX(id) AS max_id
                FROM production
                WHERE station_id = :stationId
                GROUP BY station_Id, daty
            ) latest ON p.id = latest.max_id
            ORDER BY p.daty DESC
            LIMIT :limit
        ',$rsm);
        

        $nativeQuery->setParameter('stationId', $stationId);
        $nativeQuery->setParameter('limit', $limit);

        
        $result = $nativeQuery->getResult();

        return $result;
    }

}
