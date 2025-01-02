<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire pour confirmer l'activation du compte.
 */
class ActivationFormType extends AbstractType
{


    /**
     * Construit le formulaire pour l'activation de compte.
     *
     * @param FormBuilderInterface $builder Instance pour construire le formulaire.
     * @param array                $options Options du formulaire.
     *
     * @return void
     */
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


    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver Instance pour définir les options par défaut.
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                // Ajoutez les options par défaut ici si nécessaire.
            ]
        );

    }//end configureOptions()


}//end class
