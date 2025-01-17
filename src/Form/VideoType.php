<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
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
        $builder
            ->add(
                'embedCode',
                TextareaType::class,
                [
                    'label' => 'Code d\'intégration de la vidéo',
                    'attr'  => [
                        'placeholder' => 'Exemple : <iframe ...></iframe>',
                        'rows'        => 5,
                        'class'       => 'form-control',
                    ],
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
                'data_class' => Video::class,
            ]
        );
    }


}
