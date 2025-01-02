<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les entités Comment.
 *
 * Cette classe contient des méthodes personnalisées pour interagir
 * avec la base de données concernant les entités Comment.
 */
class CommentRepository extends ServiceEntityRepository
{

    /**
     * Constructeur de la classe CommentRepository.
     *
     * @param ManagerRegistry $registry le registre pour l'accès aux entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }// end __construct()

    // **
    // * @return Comment[] Returns an array of Comment objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('c')
    // ->andWhere('c.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('c.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?Comment
    // {
    // return $this->createQueryBuilder('c')
    // ->andWhere('c.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }
}// end class
