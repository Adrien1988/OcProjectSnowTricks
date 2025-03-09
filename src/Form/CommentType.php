<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{


    /**
     * Construit le formulaire de commentaire.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array                $options Les options du formulaire
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'Votre commentaire',
                    'attr'  => [
                        'class'       => 'form-control',
                        'rows'        => 2,
                        'placeholder' => 'Ecrivez votre commentaire ici...',
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Le commentaire ne peut pas être vide.']),
                        new Length(['max' => 500, 'maxMessage' => 'Le commentaire ne peut pas dépasser {{ limit }} caractères.']),
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
                'data_class' => Comment::class,
            ]
        );
    }


}
