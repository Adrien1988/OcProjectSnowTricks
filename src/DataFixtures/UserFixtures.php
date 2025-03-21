<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Classe permettant de charger des données initiales dans la base de données.
 */
class UserFixtures extends Fixture
{
    private UserPasswordHasher $passwordHasher;


    /**
     * Constructeur.
     *
     * @param UserPasswordHasherInterface $passwordHasher service pour hacher les mots de passe des utilisateurs
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    /**
     * Charge les données initiales dans la base de données.
     *
     * @param ObjectManager $manager gestionnaire d'entités pour persister les données
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Tableau contenant les infos de plusieurs utilisateurs
        $usersData = [
            [
                'username'  => 'demo_user',
                'email'     => 'demo@example.com',
                'password'  => 'password',
                'isActive'  => true,
                'reference' => 'demo-user', // pour addReference()
            ],
            [
                'username'  => 'jane_doe',
                'email'     => 'jane@example.com',
                'password'  => 'secret123',
                'isActive'  => true,
                'reference' => 'jane-user', // exemple d’autre référence
            ],
            [
                'username'  => 'john_smith',
                'email'     => 'john@example.com',
                'password'  => 'mypassword',
                'isActive'  => true,
                'reference' => 'john-user',
            ],
            [
                'username'  => 'alex_hawk',
                'email'     => 'alex@example.com',
                'password'  => 'alexhawk',
                'isActive'  => true,
                'reference' => 'alex-user',
            ],
            [
                'username'  => 'marie_lake',
                'email'     => 'marie@example.com',
                'password'  => 'marielake',
                'isActive'  => true,
                'reference' => 'marie-user',
            ],
            [
                'username'  => 'paul_rider',
                'email'     => 'paul@example.com',
                'password'  => 'paulrider',
                'isActive'  => true,
                'reference' => 'paul-user',
            ],
            // Ajoutez autant d’utilisateurs que nécessaire
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setUsername($data['username'])
                ->setEmail($data['email'])
                ->setIsActive($data['isActive'])
                ->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

            $manager->persist($user);

            // Stocker la référence (facultatif si vous en avez besoin dans d’autres fixtures)
            $this->addReference($data['reference'], $user);
        }

        $manager->flush();
    }


}
