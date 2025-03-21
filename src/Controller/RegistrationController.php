<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ActivationFormType;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{


    /**
     * Gère l'enregistrement des utilisateurs.
     *
     * @param Request                     $request            La requête HTTP courante
     * @param UserPasswordHasherInterface $userPasswordHasher Le service de hachage des mots de passe
     * @param EntityManagerInterface      $em                 Le gestionnaire
     *                                                        d'entités Doctrine
     * @param MailerService               $mailerService      Le service d'envoi d'emails pour l'activation
     *
     * @return Response La réponse HTTP pour le formulaire d'enregistrement
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em, MailerService $mailerService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // Ajoute les autres étapes (token, etc.)
            $user->setIsActive(false);
            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);

            $em->persist($user);
            $em->flush();

            // Envoie l'email d'activation
            $mailerService->sendActivationEmail($user->getEmail(), $token);

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Vérifiez votre email pour l’activer.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form->createView(),
            ]
        );
    }


    /**
     * Affiche et traite le formulaire d'activation de compte.
     *
     * @param string                      $token          Le token d'activation
     * @param Request                     $request        La requête HTTP
     * @param EntityManagerInterface      $em             Le gestionnaire d'entités permettant d'interagir avec la base de
     *                                                    données
     * @param UserPasswordHasherInterface $passwordHasher Le service de hachage des mots de passe
     *
     * @return Response La réponse HTTP
     */
    #[Route('/activate/{token}', name: 'app_activate_account', methods: ['GET', 'POST'])]
    public function showActivationForm(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $user = $em->getRepository(User::class)->findOneBy(['activationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token invalide.');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ActivationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!isset($data['email']) || !isset($data['password'])) {
                $this->addFlash('error', 'Veuillez remplir tous les champs du formulaire.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            // Vérification de l’email et du mot de passe dans une seule condition
            if ($data['email'] !== $user->getEmail()
                || !$passwordHasher->isPasswordValid($user, $data['password'])
            ) {
                $this->addFlash('error', 'Email ou mot de passe incorrect.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            $user->setIsActive(true);
            $user->setActivationToken(null);
            $em->flush();

            $this->addFlash('success', 'Votre compte est maintenant actif !');

            return $this->redirectToRoute('app_login');
        }// end showActivationForm()

        return $this->render(
            'registration/activate.html.twig',
            [
                'activationForm' => $form->createView(),
                'token'          => $token,
            ]
        );
    }


}
