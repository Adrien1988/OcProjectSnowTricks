<?php

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Transformer pour générer un slug à partir d'une chaîne de caractères.
 */
class SlugTransformer implements DataTransformerInterface
{
    private SluggerInterface $slugger;


    /**
     * Constructeur du SlugTransformer.
     *
     * @param SluggerInterface $slugger Interface permettant de transformer une chaîne en slug
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    /**
     * Transforme la donnée d'origine (slug existant) vers une chaîne pour le formulaire.
     *
     * @param string|null $value La valeur du slug existant
     *
     * @return string La valeur transformée pour le formulaire
     */
    public function transform($value): string
    {
        return $value ?? '';
    }


    /**
     * Transforme la donnée soumise (nom) en un slug valide.
     *
     * @param string|null $value La valeur saisie à transformer en slug
     *
     * @return string Le slug généré à partir du nom
     */
    public function reverseTransform($value): string
    {
        return $value ? $this->slugger->slug($value)->lower() : '';
    }


}
