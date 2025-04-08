<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les opérations sur l'entité Figure.
 *
 * @method Figure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figure[]    findAll()
 * @method Figure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureRepository extends ServiceEntityRepository
{


    /**
     * Constructeur de la classe FigureRepository.
     *
     * @param ManagerRegistry $registry le registre pour l'accès aux entités
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figure::class);
    }// end __construct()


    /**
     * Étape 1 : Récupère juste les IDs de figure, paginés.
     */
    public function findPaginatedFigureIds(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('f')
            ->select('f.id')
            ->orderBy('f.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getScalarResult();
        // Retourne un tableau de type [ ['id' => 12], ['id' => 13], ... ]
    }


    /**
     * Étape 2 : Charge les figures (avec images) pour ces IDs.
     */
    public function findFiguresWithImages(array $ids): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.images', 'i')
            ->addSelect('i')
            ->where('f.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Récupère une figure avec ses relations (images, vidéos, commentaires) en une seule requête.
     *
     * Cette méthode utilise des jointures pour inclure les relations liées à une figure spécifique
     * en fonction de son slug. Elle optimise les performances en limitant le nombre de requêtes SQL nécessaires.
     *
     * @param string $slug le slug de la figure
     *
     * @return Figure|null la figure correspondante ou null si elle n'existe pas
     */
    public function findOneWithRelations(string $slug): ?Figure
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.images', 'i')
            ->addSelect('i')
            ->leftJoin('f.videos', 'v')
            ->addSelect('v')
            ->where('f.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }


}// end class
