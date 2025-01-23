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
     * Récupère toutes les figures avec leurs images associées en une seule requête.
     *
     * Cette méthode utilise une jointure gauche (LEFT JOIN) pour inclure les données
     * des images dans les résultats. Elle optimise les performances en limitant
     * le nombre de requêtes SQL nécessaires.
     *
     * @return Figure[] retourne un tableau contenant les figures et leurs images associées
     */
    public function findAllWithImages(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.images', 'i')
            ->addSelect('i')
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
