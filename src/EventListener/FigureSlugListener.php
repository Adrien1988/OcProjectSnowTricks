<?php

namespace App\EventListener;

use App\Entity\Figure;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureSlugListener
{
    private SluggerInterface $slugger;


    /**
     * Constructeur du listener pour générer les slugs.
     *
     * @param SluggerInterface $slugger service utilisé pour créer des slugs SEO-friendly
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    /**
     * Génère le slug avant la persistance d'une nouvelle figure.
     *
     * @param LifecycleEventArgs $args arguments de l'événement Doctrine
     *
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Figure) {
            dump('PrePersist Event Triggered');
            $entity->generateSlug($this->slugger);
        }
    }


    /**
     * Génère ou met à jour le slug avant toute modification.
     *
     * @param LifecycleEventArgs $args arguments de l'événement Doctrine
     *
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Figure) {
            $entity->generateSlug($this->slugger);
        }
    }


}
