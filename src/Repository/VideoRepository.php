<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les entités Video.
 *
 * Fournit des méthodes personnalisées pour interagir avec la base de données
 * concernant les entités Video.
 */
class VideoRepository extends ServiceEntityRepository
{


    /**
     * Constructeur de la classe VideoRepository.
     *
     * @param ManagerRegistry $registry le registre pour l'accès aux entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }// end __construct()


    // **
    // * @return Video[] Returns an array of Video objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('v')
    // ->andWhere('v.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('v.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?Video
    // {
    // return $this->createQueryBuilder('v')
    // ->andWhere('v.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }
}// end class
