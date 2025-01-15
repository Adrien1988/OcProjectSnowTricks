<?php

namespace App\Form;

use App\Entity\User;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{


    /**
     * Construit le formulaire d'inscription.
     *
     * @param FormBuilderInterface $builder instance utilisée pour construire les champs du formulaire
     * @param array                $options options supplémentaires pour le formulaire
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'username',
                null,
                [
                    'label' => 'Nom d\'utilisateur',
                    'attr'  => [
                        'class'       => 'form-control',
                        'placeholder' => 'Entrez votre nom d\'utilisateur',
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Adresse mail',
                    'attr'  => [
                        'class'       => 'form-control',
                        'placeholder' => 'Entrez votre adresse email',
                    ],
                ]
            )
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'mapped'                 => false,
                    'constraints'            => [
                        new IsTrue(
                            [
                                'message' => 'Vous devez accepter nos termes.',
                            ]
                        ),
                    ],
                    'label' => 'En m\'inscrivant à ce site j\'accepte les termes.',
                    'attr'  => [
                        'class' => 'form-check-input',
                    ],
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped'      => false,
                    'attr'        => [
                        'class'        => 'form-control',
                        'placeholder'  => 'Entrez votre mot de passe',
                        'autocomplete' => 'new-password',
                    ],
                    'label'       => 'Mot de passe',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Le mot de passe est obligatoire.',
                            ]
                        ),
                        new Length(
                            [
                                'min'        => 6,
                                'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                                'max'        => 4096,
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'avatarMethod',
                ChoiceType::class,
                [
                    'choices' => [
                        'Ajouter l\'avatar ultérieurement' => 'none',
                        'URL'                              => 'url',
                        'Uploader un fichier'              => 'upload',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'mapped'   => false,
                    'label'    => 'Comment fournir votre avatar ?',
                    'attr'     => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'avatarUrl',
                TextType::class,
                [
                    'label'    => 'URL de votre avatar',
                    'required' => false,
                    'mapped'   => false,
                    'attr'     => [
                        'class'       => 'form-control',
                        'placeholder' => 'Entrez l\'URL de votre avatar',
                    ],
                ]
            )
            ->add(
                'avatarFile',
                FileType::class,
                [
                    'required' => false,
                    'label'    => 'Fichier avatar',
                    'mapped'   => false,
                    'attr'     => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'captcha',
                CaptchaType::class,
                [
                    'label'           => 'Saisissez le code ci-dessous',
                    'reload'          => true,
                    'as_url'          => true,
                    'invalid_message' => 'Le code est invalide.',
                    'attr'            => [
                        'class' => 'form-control',
                    ],
                ]
            );
    }// end buildForm()


    /**
     * Configure les options par défaut du formulaire.
     *
     * @param OptionsResolver $resolver instance pour définir les options
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }// end configureOptions


}
