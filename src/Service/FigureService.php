<?php

namespace App\Service;

use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FigureService
{
    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;


    /**
     * Constructeur du service FigureService.
     *
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     * @param UrlGeneratorInterface  $urlGenerator  Générateur d'URL Symfony
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;

    }


    /**
     * Trouve une figure par son ID ou lance une exception.
     *
     * @param int $id L'identifiant de la figure
     *
     * @return Figure La figure trouvée
     */
    public function findFigureById(int $id): Figure
    {
        $figure = $this->entityManager->getRepository(Figure::class)->find($id);

        if (!$figure) {
            throw new \Exception('La figure demandée n\'existe pas.');
        }

        return $figure;
    }


    /**
     * Sauvegarde ou supprime une entité en base de données.
     *
     * @param object $entity L'entité à sauvegarder ou supprimer
     * @param bool   $remove Indique si l'entité doit être supprimée (true) ou sauvegardée (false)
     *
     * @return bool True si l'opération réussit, sinon False
     */
    public function saveEntity(object $entity, bool $remove = false): bool
    {
        try {
            if ($remove) {
                $this->entityManager->remove($entity);
            } else {
                $this->entityManager->persist($entity);
            }

            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Redirige vers la page de détail d'une figure.
     *
     * @param Figure $figure La figure vers laquelle rediriger
     *
     * @return RedirectResponse La redirection vers la page de détail
     */
    public function redirectToFigureDetail(Figure $figure): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('app_figure_detail', ['slug' => $figure->getSlug()]));
    }


}
