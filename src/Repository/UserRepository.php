<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les entités User.
 *
 * Fournit des méthodes personnalisées pour interagir avec la base de données
 * concernant les entités User.
 */
class UserRepository extends ServiceEntityRepository
{

    /**
     * Constructeur de la classe UserRepository.
     *
     * @param ManagerRegistry $registry le registre pour l'accès aux entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }// end __construct()

    // **
    // * @return User[] Returns an array of User objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('u')
    // ->andWhere('u.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('u.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?User
    // {
    // return $this->createQueryBuilder('u')
    // ->andWhere('u.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }
}// end class
