<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivationFormType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Confirmez votre email',
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Confirmez votre mot de passe',
                ]
            );

    }//end buildForm()


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            ]
        );

    }//end configureOptions()


}//end class
