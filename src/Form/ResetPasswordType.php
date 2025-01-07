<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordType extends AbstractType
{


    /**
     * Construit le formulaire pour définir un nouveau mot de passe.
     *
     * @param FormBuilderInterface $builder L'instance du constructeur de formulaire
     * @param array                $options Les options pour le formulaire
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'label'       => 'Nouveau mot de passe',
                    'mapped'      => false,
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Veuillez entrer un mot de passe.',
                            ]
                        ),
                        new Length(
                            [
                                'min'        => 6,
                                'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                                'max'        => 4096,
                            ]
                        ),
                    ],
                ]
            );
    }


    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         // Configure your form options here
    //     ]);
    // }
}
