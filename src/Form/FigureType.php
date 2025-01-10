<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom de la figure',
                    'attr'  => [
                        'placeholder' => 'Entrez le nom de la figure',
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description',
                    'attr'  => [
                        'placeholder' => 'Décrivez la figure',
                    ],
                ]
            )
            ->add('slug')
            ->add(
                'figureGroup',
                TextType::class,
                [
                    'label' => 'Groupe de la figure',
                    'attr'  => [
                        'placeholder' => 'Indiquez le groupe de la figure',
                    ],
                ]
            )
            ->add(
                'images',
                FileType::class,
                [
                    'label'    => 'Images',
                    'multiple' => true,
                    'mapped'   => false,
                    'required' => false,
                    'attr'     => [
                        'accept' => 'image/*',
                    ],
                ]
            )
            ->add(
                'videos',
                TextType::class,
                [
                    'label'    => 'Videos (URLs ou embeds)',
                    'required' => false,
                    'attr'     => ['placeholder' => 'Collez les URLs des videos'],
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
