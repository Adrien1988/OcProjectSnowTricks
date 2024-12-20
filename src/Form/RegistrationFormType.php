<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmez le mot de passe'],
            ])
            ->add('avatarMethod', ChoiceType::class, [
                'choices' => [
                    'Ajouter l\'avatar ultérieurement'  => 'none',
                    'URL' => 'url',
                    'Uploader un fichier' => 'upload',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'label' => 'Comment fournir votre avatar ?'
            ])
            ->add('avatarUrl', TextType::class, [
                'label' => 'URL de votre avatar',
                'required' => false,
                'mapped' => false,
            ])
            ->add('avatarFile', FileType::class, [
                'required' => false,
                'label' => 'Fichier avatar',
                'mapped' => false,
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Saisissez le code ci-dessous',
                'reload' => true,
                'as_url' => true,
                'invalid_message' => 'Le code est invalide.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
