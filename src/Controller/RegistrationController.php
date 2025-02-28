<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ActivationFormType;
use App\Form\RegistrationFormType;
use App\Service\EntityService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends BaseController
{


    /**
     * Gère l'enregistrement des utilisateurs.
     *
     * @param Request                     $request            La requête HTTP courante
     * @param UserPasswordHasherInterface $userPasswordHasher Le service de hachage des mots de passe
     * @param EntityService               $entityService      Service pour la gestion des entités
     * @param MailerService               $mailerService      Le service d'envoi d'emails pour l'activation
     *
     * @return Response La réponse HTTP pour le formulaire d'enregistrement
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityService $entityService, MailerService $mailerService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $response = $this->handleFormSubmission($request, $form, 'Votre compte a été créé avec succès ! Veuillez vérifier votre email pour l’activer.', 'app_register', [], true, true);
        if ($response) {
            return $response;
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render(
                'registration/register.html.twig',
                [
                    'registrationForm' => $form->createView(),
                ]
            );
        }

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

        // Sauvegarde en base de données
        $entityService->saveEntity($user);

        // Envoie l'email d'activation
        $mailerService->sendActivationEmail($user->getEmail(), $token);

        return $this->redirectToRoute('app_login');

    }// end register()


    /**
     * Affiche et traite le formulaire d'activation de compte.
     *
     * @param string                      $token          Le token d'activation
     * @param Request                     $request        La requête HTTP
     * @param EntityManagerInterface      $entityManager  Le gestionnaire d'entités permettant d'interagir avec la base de données
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
        $response = $this->handleFormSubmission($request, $form, 'Votre compte est maintenant actif !', 'app_activate_account', ['token' => $token], true, true);
        if ($response) {
            return $response;
        }

        if ($form->isSubmitted()) {
            $data = $form->getData() ?? [];
            if (!isset($data['email']) || !isset($data['password'])) {
                $this->addFlash('error', 'Veuillez remplir tous les champs du formulaire.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            if ($data['email'] !== $user->getEmail()) {
                $this->addFlash('error', 'L’email ne correspond pas à celui associé au compte.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
                $this->addFlash('error', 'Le mot de passe est incorrect.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            $user->setIsActive(true);
            $user->setActivationToken(null);
            $entityManager->flush();

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
