<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class EntityService
{


    /**
     * Constructeur du service FigureService.
     *
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }


    /**
     * Trouve une entité par sa classe et son ID.
     *
     * @param string $entityClass La classe de l'entité
     * @param int    $id          L'identifiant de l'entité
     *
     * @return object|null L'entité trouvée ou null si non trouvée
     */
    public function findEntityById(string $entityClass, int $id): ?object
    {
        return $this->entityManager->getRepository($entityClass)->find($id);
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
            }

            if (!$remove) {
                $this->entityManager->persist($entity);
            }

            $this->entityManager->flush();

            return true;
        } catch (\Exception) {
            return false;
        }
    }


}
