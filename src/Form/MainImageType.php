<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainImageType extends AbstractType
{


    /**
     * Construit le formulaire pour ajouter une vidéo.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array                $options Les options supplémentaires
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $figure = $options['figure'];

        // Associe les IDs aux URLs
        $choices = [];
        $choicesAttributes = [];
        foreach ($figure->getImages() as $image) {
            $choices[$image->getId()] = $image->getId();
            $choicesAttributes[$image->getId()] = ['data-image-url' => $image->getUrl()];
        }

        $builder
            ->add(
                'mainImage',
                ChoiceType::class,
                [
                    'choices'     => $choices,
                    'expanded'    => true,
                    'multiple'    => false,
                    'label'       => false,
                    'choice_attr' => $choicesAttributes, // Ajoute les URLs ici
                ]
            )
            ->add(
                'referer',
                HiddenType::class,
                [
                    'mapped' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Définir comme image principale',
                    'attr'  => ['class' => 'btn btn-primary'],
                ]
            );
    }


    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver Le résolveur d'options
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'figure' => null,
            ]
        );
    }


}
