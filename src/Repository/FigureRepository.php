<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Figure>
 */
class FigureRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figure::class);

    }//end __construct()


    // **
    // * @return Figure[] Returns an array of Figure objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('f')
    // ->andWhere('f.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('f.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?Figure
    // {
    // return $this->createQueryBuilder('f')
    // ->andWhere('f.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }
}//end class
