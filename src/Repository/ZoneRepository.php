<?php

namespace App\Repository;

use App\Entity\Site;
use App\Entity\Zone;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Zone>
 *
 * @method Zone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Zone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Zone[]    findAll()
 * @method Zone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
    }

    public function insert($zone): void
    {
        $sql = "
            INSERT INTO zone (libelle, description, coord) 
            VALUES (:libelle, :description, ST_GeomFromText(:polygon ,4326))
        ";

        // Exécution de la requête avec executeStatement pour obtenir le nombre de lignes affectées
        $result = $this->getEntityManager()->getConnection()->executeStatement($sql, [
            'libelle' => $zone->getLibelle(),
            'description' => $zone->getDescription(),
            'polygon' => $zone->getCoordToWKT(),
        ]);
    }

    public function getSitesInZone($zoneId)
    {
        $entityManager = $this->getEntityManager();

         // Créez un ResultSetMapping
         $rsm = new ResultSetMappingBuilder($this->getEntityManager());

         // Ajoutez le mapping pour l'entité ZoneVente
         $rsm->addRootEntityFromClassMetadata('App\Entity\Site', 's');

        $nativeQuery = $entityManager->createNativeQuery('
            SELECT s.*
            FROM site s
            JOIN zone z ON ST_Intersects(s.coord, z.coord)
            WHERE z.id = :zoneId
        ',$rsm);
        

        $nativeQuery->setParameter('zoneId', $zoneId);
        
        $result = $nativeQuery->getResult();

        return $result;
    }


//    /**
//     * @return Zone[] Returns an array of Zone objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('z')
//            ->andWhere('z.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('z.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Zone
//    {
//        return $this->createQueryBuilder('z')
//            ->andWhere('z.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
