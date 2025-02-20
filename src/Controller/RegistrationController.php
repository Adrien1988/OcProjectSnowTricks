<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ActivationFormType;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
     * @param Security                    $security           Le service de gestion de la connexion utilisateur
     * @param EntityManagerInterface      $entityManager      Le gestionnaire d'entités pour persister les données utilisateur
     * @param MailerService               $mailerService      Le service d'envoi d'emails pour l'activation
     *
     * @return Response La réponse HTTP pour le formulaire d'enregistrement
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, MailerService $mailerService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*
             * @var string $plainPassword
             */

            $plainPassword = $form->get('plainPassword')->getData();

            // Encode le mot de passe en clair
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Ajoute les autres étapes (token, etc.)
            $user->setIsActive(false);
            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);

            $entityManager->persist($user);
            try {
                $entityManager->flush();

                // Envoie l'email d'activation
                $mailerService->sendActivationEmail($user->getEmail(), $token);

                $this->addFlash('success', 'Votre compte a été créé avec succès ! Veuillez vérifier votre email pour l’activer.');

                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création du compte.');
            }
        }

        // Si le formulaire est soumis mais non valide, on affiche les détails des erreurs
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs suivantes : '.implode(' - ', $errors));
            }
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form->createView(),
            ]
        );
    }// end register()


    /**
     * Affiche et traite le formulaire d'activation de compte.
     *
     * @param string                      $token          Le token d'activation
     * @param Request                     $request        La requête HTTP
     * @param EntityManagerInterface      $entityManager  Le gestionnaire d'entités
     * @param UserPasswordHasherInterface $passwordHasher Le service de hachage des mots de passe
     *
     * @return Response La réponse HTTP
     */
    #[Route('/activate/{token}', name: 'app_activate_account', methods: ['GET', 'POST'])]
    public function showActivationForm(
        string $token,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $user = $entityManager->getRepository(User::class)->findOneBy(['activationToken' => $token]);

        if (null === $user) {
            $this->addFlash('error', 'Token invalide.');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ActivationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            $enteredPassword = $data['password'];

            if ($email !== $user->getEmail()) {
                $this->addFlash('error', 'L’email ne correspond pas à celui associé au compte.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            if (false === $passwordHasher->isPasswordValid($user, $enteredPassword)) {
                $this->addFlash('error', 'Le mot de passe est incorrect.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            $user->setIsActive(true);
            $user->setActivationToken(null);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte est maintenant actif !');

            return $this->redirectToRoute('app_login');
        }

        // Si le formulaire est soumis mais non valide, on affiche les détails des erreurs
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs suivantes : '.implode(' - ', $errors));
            }
        }

        return $this->render(
            'registration/activate.html.twig',
            [
                'activationForm' => $form->createView(),
                'token'          => $token,
            ]
        );
    }// end showActivationForm()


}
