<?php

namespace App\Form;

use App\DataTransformer\SlugTransformer;
use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureType extends AbstractType
{

    private SlugTransformer $slugTransformer;


    /**
     * Constructeur du formulaire FigureType.
     *
     * @param SlugTransformer $slugTransformer Transforme le champ 'name' en un slug valide
     */
    public function __construct(SlugTransformer $slugTransformer)
    {
        $this->slugTransformer = $slugTransformer;
    }


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
            ->add(
                'figureGroup',
                TextType::class,
                [
                    'label' => 'Groupe de la figure',
                    'attr'  => [
                        'placeholder' => 'Indiquez le groupe de la figure',
                    ],
                ]
            );

        // Appliquer le transformer au champ "name"
        $builder->get('name')->addModelTransformer($this->slugTransformer);
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
