<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Classe permettant de charger des données de commentaires réalistes pour chaque figure et chaque utilisateur.
 */
class CommentFixtures extends Fixture implements DependentFixtureInterface
{


    /**
     * Charge les données de commentaires dans la base.
     *
     * @param ObjectManager $manager Le gestionnaire d’entités Doctrine
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Références d’utilisateurs créés dans UserFixtures
        // Assurez-vous d’avoir addReference('demo-user', $demoUser), etc. dans UserFixtures
        $userReferences = [
            'demo-user',
            'jane-user',
            'john-user',
            'alex-user',
            'marie-user',
            'paul-user',
        ];

        // Quelques contenus de commentaires variés (en français)
        $commentsPool = [
            "J'adore cette figure, je vais essayer de la reproduire ce week-end !",
            "Wow, c'est super impressionnant !",
            'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.',
            'Merci pour le partage, je ne connaissais pas cette variante.',
            'Toujours un plaisir de voir de nouvelles figures !',
            'Pas mal du tout, tu gères vraiment.',
            "J'ai encore du mal avec le timing, mais ce tuto m'aide beaucoup.",
            "Félicitations, c'est un beau trick !",
            "Ça a l'air simple en théorie, mais en pratique c'est chaud !",
            "Trop stylé, j'ajoute ça à ma liste de figures à apprendre.",
            'Est-ce que tu conseillerais un stance plus large pour ce trick ?',
            "Magnifique, j'aime le style old school.",
            "Je préfère les rotations désaxées, mais j'avoue que ça en jette.",
        ];

        // On suppose qu’il y a 10 figures dans FigureFixtures (figure-0 à figure-9)
        for ($i = 0; $i < 10; ++$i) {

            /** @var Figure $figure */
            $figure = $this->getReference('figure-'.$i, Figure::class);

            // Pour chaque utilisateur, on va créer plusieurs commentaires
            foreach ($userReferences as $userRef) {

                /** @var User $user */
                $user = $this->getReference($userRef, User::class);

                // Générer 1 à 3 commentaires pour chaque user/figure
                $nbComments = rand(1, 3);
                for ($k = 1; $k <= $nbComments; ++$k) {
                    $comment = new Comment();

                    // Choisir aléatoirement un contenu dans $commentsPool
                    $randomContent = $commentsPool[array_rand($commentsPool)];

                    $comment
                        ->setContent($randomContent)
                        ->setAuthor($user)
                        ->setFigure($figure);

                    // Facultatif : On peut gérer la date de création
                    // ex. entre -30 jours et maintenant
                    $daysAgo = rand(0, 30);
                    $randomCreatedAt = (new \DateTimeImmutable())->modify("-{$daysAgo} days");
                    $comment->setCreatedAt($randomCreatedAt);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }


    /**
     * Indique la liste des fixtures dont dépend CommentFixtures.
     *
     * @return array<class-string> liste des classes de fixtures parent
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            FigureFixtures::class,
        ];
    }


}
