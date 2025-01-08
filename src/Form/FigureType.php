<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureType extends AbstractType
{


    /**
     * Construit le formulaire pour une figure.
     *
     * Définit les champs du formulaire de création/modification d'une figure.
     *
     * @param FormBuilderInterface $builder constructeur du formulaire
     * @param array                $options options du formulaire
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('slug')
            ->add('figureGroup')
            ->add(
                'createdAt',
                null,
                [
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'updatedAt',
                null,
                [
                    'widget' => 'single_text',
                ]
            );
    }


    /**
     * Configure les options du formulaire.
     *
     * Associe le formulaire à l'entité Figure.
     *
     * @param OptionsResolver $resolver résolveur des options
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Figure::class,
            ]
        );
    }


}
