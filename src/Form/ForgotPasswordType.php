<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ForgotPasswordType extends AbstractType
{


    /**
     * Construit le formulaire pour la fonctionnalité de réinitialisation de mot de passe.
     *
     * @param FormBuilderInterface $builder L'instance du constructeur de formulaire
     * @param array                $options Les options pour le formulaire
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Adresse email']);
    }


    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults(
    //         [
    //         // Configure your form options here
    //         ]
    //     );
    // }


}
