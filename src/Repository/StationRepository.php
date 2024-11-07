<?php

namespace App\Repository;

use App\Entity\Station;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Station>
 *
 * @method Station|null find($id, $lockMode = null, $lockVersion = null)
 * @method Station|null findOneBy(array $criteria, array $orderBy = null)
 * @method Station[]    findAll()
 * @method Station[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }

    public function insert(Station $station): int
    {
        // Construction de la requête d'insertion
        $query = "
            INSERT INTO station (libelle, code,site_id, coord) 
            VALUES (:libelle, :code, :site_id, ST_GeomFromText(CONCAT('POINT(', :latitude, ' ', :longitude, ')'), 4326))
        ";

        // Exécution de la requête avec executeStatement pour obtenir le nombre de lignes affectées
        $result = $this->getEntityManager()->getConnection()->executeStatement($query, [
            'libelle' => $station->getLibelle(),
            'code' => $station->getCode(),
            'site_id' => $station->getSite()->getId(),
            'latitude' => $station->getLatitude(),
            'longitude' => $station->getLongitude(),
        ]);
        // Récupération de l'ID de la station nouvellement insérée
        return (int) $this->getEntityManager()->getConnection()->lastInsertId();
    }
    public function insertStationSourceRelation(int $stationId, int $sourceId): void
    {
        $query = "INSERT INTO source_station (source_id,station_id) VALUES (:source_id , :station_id)";
        
        $this->getEntityManager()->getConnection()->executeStatement($query, [
            'station_id' => $stationId,
            'source_id' => $sourceId,
        ]);
    }
    public function update(Station $station): void
    {
        // Construction de la requête de mise à jour
        $query = "
            UPDATE station 
            SET libelle = :libelle,
                code = :code,
                site_id = :site_id,
                coord = ST_GeomFromText(CONCAT('POINT(', :latitude, ' ', :longitude, ')'), 4326)
            WHERE id = :id
        ";

        // Exécution de la requête avec executeStatement pour obtenir le nombre de lignes affectées
        $result = $this->getEntityManager()->getConnection()->executeStatement($query, [
            'libelle' => $station->getLibelle(),
            'code' => $station->getCode(),
            'site_id' => $station->getSite()->getId(),
            'latitude' => $station->getLatitude(),
            'longitude' => $station->getLongitude(),
            'id' => $station->getId(), // On utilise l'ID de la station pour identifier l'enregistrement à mettre à jour
        ]);
    }

    public function getZoneOfStation($stationId)
    {
        $entityManager = $this->getEntityManager();

         // Créez un ResultSetMapping
         $rsm = new ResultSetMappingBuilder($this->getEntityManager());

         // Ajoutez le mapping pour l'entité ZoneVente
         $rsm->addRootEntityFromClassMetadata('App\Entity\Site', 'z');

        $nativeQuery = $entityManager->createNativeQuery('
            SELECT z.*
            FROM station s
            JOIN site z ON ST_Intersects(s.coord, z.coord)
            WHERE s.id = :stationID
        ',$rsm);
        

        $nativeQuery->setParameter('stationId', $stationId);
        
        $result = $nativeQuery->getResult();

        return $result;
    }
//    /**
//     * @return Station[] Returns an array of Station objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Station
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
