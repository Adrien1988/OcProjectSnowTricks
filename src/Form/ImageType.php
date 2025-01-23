<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImageType extends AbstractType
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
            'file',
            FileType::class,
            [
                'label'       => 'Télécharger une image',
                'mapped'      => false,
                'required'    => true,
                'constraints' => [
                    new File(
                        [
                            'maxSize'   => '2M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG).',
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
                'data_class' => Image::class,
            ]
        );
    }


}
