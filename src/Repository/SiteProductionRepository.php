<?php

namespace App\Repository;

use App\Entity\SiteProduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<SiteProduction>
 */
class SiteProductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteProduction::class);
    }

    public function findProductionsBySite(int $siteId)
    {
        
            $entityManager = $this->getEntityManager();

            // Créez un ResultSetMapping
            $rsm = new ResultSetMappingBuilder($this->getEntityManager());
   
            // Ajoutez le mapping pour l'entité ZoneVente
            $rsm->addRootEntityFromClassMetadata('App\Entity\SiteProduction', 'p');
   
           $nativeQuery = $entityManager->createNativeQuery('
               SELECT p.*
               FROM siteProduction p
               where site_id = :siteId
               ORDER BY p.date_production DESC
               LIMIT 10
           ',$rsm);
           
   
           $nativeQuery->setParameter('siteId', $siteId);
           
           $result = $nativeQuery->getResult();
           //dd($result);
   
           return $result;
    }
}
