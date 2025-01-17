<?php

namespace App\Form;

use App\Entity\Video;
use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class VideoType extends AbstractType
{

    /**
     * Construit le formulaire pour ajouter une vidéo.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array $options Les options supplémentaires
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('embedCode', TextareaType::class, [
                'label' => 'Code d\'intégration de la vidéo',
                    'attr' => [
                        'placeholder' => 'Exemple : <iframe ...></iframe>',
                        'rows' => 5,
                        'class' => 'form-control',
                    ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
