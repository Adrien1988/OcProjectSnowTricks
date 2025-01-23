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


    /**
     * Récupère les commentaires d'une figure avec pagination et tri.
     *
     * @param int $figureId L'identifiant de la figure
     * @param int $page     Le numéro de la page
     * @param int $limit    Le nombre de commentaires par page
     *
     * @return array Un tableau contenant les commentaires paginés
     */
    public function findByFigureWithPagination(int $figureId, int $page = 1, int $limit = 10): array
    {
        try {
            $query = $this->createQueryBuilder('c')
                ->where('c.figure = :figureId')
                ->setParameter('figureId', $figureId)
                ->orderBy('c.createdAt', 'DESC')
                ->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit)
                ->getQuery();

            // Requête pour compter le nombre total de commentaires
            $totalComments = $this->createQueryBuilder('c')
                ->select('COUNT(c.id)')
                ->where('c.figure = :figureId')
                ->setParameter('figureId', $figureId)
                ->getQuery()
                ->getSingleScalarResult();

            // Calculez le nombre total de pages, au minimum 1
            $lastPage = max((int) ceil($totalComments / $limit), 1);

            return [
                'items'       => $query->getResult(),
                'currentPage' => $page,
                'lastPage'    => $lastPage,
            ];
        } catch (\Exception $e) {
            return [
                'items'       => [],
                'currentPage' => 1,
                'lastPage'    => 1,
            ];
        }
    }


}// end class
