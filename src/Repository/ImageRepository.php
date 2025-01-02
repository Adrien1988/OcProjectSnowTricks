<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les entités Image.
 *
 * Fournit des méthodes personnalisées pour interagir avec la base de données
 * concernant les entités Image.
 */
class ImageRepository extends ServiceEntityRepository
{

    /**
     * Constructeur de la classe ImageRepository.
     *
     * @param ManagerRegistry $registry le registre pour l'accès aux entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }// end __construct()

    // **
    // * @return Image[] Returns an array of Image objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('i')
    // ->andWhere('i.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('i.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?Image
    // {
    // return $this->createQueryBuilder('i')
    // ->andWhere('i.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }
}// end class
