<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Image;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class FigureFixtures extends Fixture implements DependentFixtureInterface
{


    /**
     * Charge les données de test dans la base de données.
     *
     * @param ObjectManager $manager gestionnaire d'entités Doctrine
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {

        $filesystem = new Filesystem();

        // 1) Récupérer la liste de toutes les images disponibles dans /images/
        $imagesDir = __DIR__.'/images';
        // on scanne le dossier pour avoir tous les fichiers
        $allImages = array_diff(scandir($imagesDir), ['.', '..']);
        // Filtrer si besoin pour ne garder que .jpg, .png, etc.
        $allImages = array_filter(
            $allImages,
            function ($file) {
                return preg_match('/\.(jpg|jpeg|png)$/i', $file);
            }
        );
        // Convertir en tableau indexé 0,1,2,...
        $allImages = array_values($allImages);

        // Liste des références d'utilisateurs créés dans UserFixtures
        // (assure-toi d’avoir un addReference('demo-user', $user) etc. pour chacun)
        $userReferences = [
            'demo-user',
            'jane-user',
            'john-user',
            'alex-user',
            'marie-user',
            'paul-user',
        ];

        // Liste des figures
        $figures = [
            [
                'name'        => 'Mute',
                'description' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
                'group'       => 'Grabs',
            ],
            [
                'name'        => 'Indy',
                'description' => 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.',
                'group'       => 'Grabs',
            ],
            [
                'name'        => 'Backflip',
                'description' => 'Rotation verticale en arrière.',
                'group'       => 'Flips',
            ],
            [
                'name'        => 'Frontside 360',
                'description' => 'Rotation horizontale de 360 degrés en frontside.',
                'group'       => 'Rotations',
            ],
            [
                'name'        => 'Method Air',
                'description' => 'Old school : saisir la carre backside en fléchissant les jambes, corps tendu.',
                'group'       => 'Old School',
            ],
            [
                'name'        => 'Cork 720',
                'description' => 'Rotation désaxée de deux tours complets agrémentée d’un grab.',
                'group'       => 'Rotations désaxées',
            ],
            [
                'name'        => 'Nose Slide',
                'description' => 'Slide sur une barre avec l’avant de la planche.',
                'group'       => 'Slides',
            ],
            [
                'name'        => 'Tail Grab',
                'description' => 'Saisie de la partie arrière de la planche, avec la main arrière.',
                'group'       => 'Grabs',
            ],
            [
                'name'        => 'Truck Driver',
                'description' => 'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).',
                'group'       => 'Grabs',
            ],
            [
                'name'        => 'Rocket Air',
                'description' => 'Old school : saisir l’avant de la planche avec les deux mains.',
                'group'       => 'Old School',
            ],
        ];

        // Générer 10 figures
        foreach ($figures as $i => $data) {
            $figure = new Figure();
            $figure->setName($data['name'])
                ->setDescription($data['description'])
                ->setFigureGroup($data['group'])
                ->setSlug(strtolower(str_replace(' ', '-', $data['name'])));

            // Choisir un utilisateur au hasard dans $userReferences
            $randomUserRef = $userReferences[array_rand($userReferences)];
            $user = $this->getReference($randomUserRef, User::class);
            $figure->setAuthor($user);

            // 2) Sélection aléatoire de N images dans $allImages
            $nbImagesWanted = 3; // ex. 3 images par figure
            // S’il y a moins d'images que $nbImagesWanted, on peut ajuster
            $nbImagesWanted = min($nbImagesWanted, count($allImages));

            // array_rand($allImages, $nbImagesWanted) renvoie une clé (ou un tableau de clés)
            $randomKeys = (array) array_rand($allImages, $nbImagesWanted);

            // Conserver les entités Image pour ensuite choisir l'une d'entre elles
            $createdImages = [];

            // 3) Pour chacune de ces images, on copie + crée l'entité
            foreach ($randomKeys as $key) {
                $randomFilename = $allImages[$key];
                $sourcePath = $imagesDir.'/'.$randomFilename;

                // On définit un nom de fichier final
                $targetFilename = uniqid('figure_').'_'.$randomFilename;
                $targetPath = __DIR__.'/../../public/uploads/'.$targetFilename;

                $image = new Image();
                $image->setFigure($figure)
                    ->setAltText("Image aléatoire pour {$data['name']}");

                if ($filesystem->exists($sourcePath)) {
                    try {
                        $filesystem->copy($sourcePath, $targetPath, true);
                    } catch (IOExceptionInterface $exception) {
                        dump("Erreur lors de la copie de $sourcePath vers $targetPath : ".$exception->getMessage());
                    }

                    // Définir l'URL correspondant
                    $image->setUrl('uploads/'.$targetFilename);
                } else {
                    $image->setUrl('uploads/default.jpg');
                }

                $createdImages[] = $image;
                $manager->persist($image);
            }

            // Choisir UNE image parmi les 3 pour la mettre en image principale
            if (!empty($createdImages)) {
                $mainImage = $createdImages[array_rand($createdImages)];
                $figure->setMainImage($mainImage);
            }

            // Ajouter une vidéo
            $video = new Video();
            $video->setEmbedCode('<iframe width="560" height="315" src="https://www.youtube.com/embed/example" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>')
                ->setFigure($figure);
            $manager->persist($video);

            $manager->persist($figure);

            $this->addReference('figure-'.$i, $figure);
        }

        $manager->flush();
    }


    /**
     * Indique la liste des fixtures dont dépend FigureFixtures.
     *
     * @return array<class-string> Liste des classes de fixtures parent
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }


}
