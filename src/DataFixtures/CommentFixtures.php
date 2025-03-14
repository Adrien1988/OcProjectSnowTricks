<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Classe permettant de charger des données de commentaires pour chaque figure et chaque utilisateur.
 */
class CommentFixtures extends Fixture implements DependentFixtureInterface
{


    /**
     * Charge les données de commentaires dans la base.
     *
     * @param ObjectManager $manager gestionnaire d’entités
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Supposons que vous avez déjà créé plusieurs utilisateurs dans UserFixtures :
        // - "demo-user"
        // - "jane-user"
        // - "john-user"
        // Ajustez cette liste de références selon vos besoins
        $userReferences = [
            'demo-user',
            'jane-user',
            'john-user',
        ];

        // On suppose qu’il y a 10 figures dans FigureFixtures
        for ($i = 0; $i < 10; ++$i) {
            // Récupérer la figure
            $figure = $this->getReference('figure-'.$i, Figure::class);

            // Pour chaque utilisateur (référence), on va créer plusieurs commentaires
            foreach ($userReferences as $userRef) {
                /*
                 * @var User $user
                 */

                $user = $this->getReference($userRef, User::class);

                // Ajouter, par exemple, 2 commentaires
                for ($k = 1; $k <= 2; ++$k) {
                    $comment = new Comment();
                    $comment->setContent("Commentaire $k de {$user->getUsername()} pour la figure $i")
                        ->setAuthor($user)
                        ->setFigure($figure);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }


    /**
     * Indique la liste des fixtures dont dépend CommentFixtures.
     *
     * @return array<string> liste des classes de fixtures parent
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            FigureFixtures::class,
        ];
    }


}
