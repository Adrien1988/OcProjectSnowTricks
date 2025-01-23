<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


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
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => "Le code d'intégration est obligatoire.",
                            ]
                        ),
                        new Length(
                            [
                                'max'        => 1000,
                                'maxMessage' => "Le code d'intégration ne doit pas dépasser {{ limit }} caractères.",
                            ]
                        ),
                        new Regex(
                            [
                                'pattern' => "/<iframe.*>.*<\/iframe>/",
                                'message' => "Le code d'intégration doit être un iframe valide.",
                            ]
                        ),
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
