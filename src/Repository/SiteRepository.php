<?php

namespace App\Repository;

use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Site>
 *
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findAll()
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }


    public function insert(Site $pointVente): void
    {
        // Construction de la requête d'insertion
        $query = "
            INSERT INTO site (libelle, adresse, coord) 
            VALUES (:libelle, :adresse, ST_GeomFromText(CONCAT('POINT(', :latitude, ' ', :longitude, ')'), 4326))
        ";

        // Exécution de la requête avec executeStatement pour obtenir le nombre de lignes affectées
        $result = $this->getEntityManager()->getConnection()->executeStatement($query, [
            'libelle' => $pointVente->getLibelle(),
            'adresse' => $pointVente->getAdresse(),
            'latitude' => $pointVente->getLatitude(),
            'longitude' => $pointVente->getLongitude(),
        ]);
    }

//    /**
//     * @return Site[] Returns an array of Site objects
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

//    public function findOneBySomeField($value): ?Site
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
