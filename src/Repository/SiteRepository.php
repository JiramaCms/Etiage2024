<?php

namespace App\Repository;

use App\Entity\Site;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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


    public function insert($site): void
    {
        $sql = "
            INSERT INTO site (libelle, adresse, coord) 
            VALUES (:libelle, :adresse, ST_GeomFromText(:polygon ,4326))
        ";

        // Exécution de la requête avec executeStatement pour obtenir le nombre de lignes affectées
        $result = $this->getEntityManager()->getConnection()->executeStatement($sql, [
            'libelle' => $site->getLibelle(),
            'adresse' => $site->getAdresse(),
            'polygon' => $site->getCoordToWKT(),
        ]);
    }
    public function update($site): void
    {
        $sql = "
            UPDATE site 
            SET libelle = :libelle, 
                adresse = :adresse, 
                coord = ST_GeomFromText(:polygon, 4326)
            WHERE id = :id
        ";

        // Exécution de la requête avec executeStatement pour obtenir le nombre de lignes affectées
        $result = $this->getEntityManager()->getConnection()->executeStatement($sql, [
            'libelle' => $site->getLibelle(),
            'adresse' => $site->getAdresse(),
            'polygon' => $site->getCoordToWKT(),
            'id' => $site->getId(),  // Assure-toi que tu as l'ID du site pour identifier l'enregistrement à mettre à jour
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
