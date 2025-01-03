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
     * Builds the registration form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The form options
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
                ]
            )
            ->add('email', EmailType::class, ['label' => 'Adresse mail'])
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'mapped'                 => false,
                    'constraints'            => [
                        new IsTrue(
                            [
                                'message' => 'You should agree to our terms.',
                            ]
                        ),
                    ],
                    'label' => 'En m\'inscrivant à ce site j\'accepte les termes.',
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped'      => false,
                    'attr'        => ['autocomplete' => 'new-password'],
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
                ]
            )
            ->add(
                'avatarUrl',
                TextType::class,
                [
                    'label'    => 'URL de votre avatar',
                    'required' => false,
                    'mapped'   => false,
                ]
            )
            ->add(
                'avatarFile',
                FileType::class,
                [
                    'required' => false,
                    'label'    => 'Fichier avatar',
                    'mapped'   => false,
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
                ]
            );
    }// end buildForm()


    /**
     * Configures the options for the registration form.
     *
     * @param OptionsResolver $resolver The options resolver
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
