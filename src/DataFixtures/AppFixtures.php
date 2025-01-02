<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Classe permettant de charger des données initiales dans la base de données.
 */
class AppFixtures extends Fixture
{


    /**
     * Charge les données initiales dans la base de données.
     *
     * @param ObjectManager $manager gestionnaire d'entités pour persister les données
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Exemple de création et de persistance d'une entité :
        // $product = new Product().
        // $manager->persist($product).
        $manager->flush();
    }// end load()


}// end class
