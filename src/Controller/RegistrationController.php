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

/**
 * Contrôleur pour la gestion de l'enregistrement et de l'activation des comptes utilisateurs.
 */
class RegistrationController extends AbstractController
{

    /**
     * Affiche et traite le formulaire d'enregistrement.
     *
     * @param Request                     $request        la
     *                                                    requête
     *                                                    HTTP
     * @param EntityManagerInterface      $entityManager  le gestionnaire
     *                                                    d'entités
     * @param UserPasswordHasherInterface $passwordHasher le service de hachage de mots de passe
     * @param MailerService               $mailerService  le service d'envoi d'emails
     *
     * @return Response la réponse HTTP
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerService $mailerService,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            // Vérification de l'unicité de l'email et du username.
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if (null !== $existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');

                return $this->redirectToRoute('app_register');
            }

            // Hashage du mot de passe.
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            // Activation par email.
            $user->setIsActive(false);

            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);

            $avatarMethod = $form->get('avatarMethod')->getData();

            if ('url' === $avatarMethod) {
                $avatarUrl = $form->get('avatarUrl')->getData();
                if (null !== $avatarUrl) {
                    $user->setAvatarUrl($avatarUrl);
                }
            }

            if ('upload' === $avatarMethod) {
                $avatarFile = $form->get('avatarFile')->getData();
                if (null !== $avatarFile) {
                    $newFilename = uniqid().'.'.$avatarFile->guessExtension();
                    $avatarFile->move($this->getParameter('avatars_directory'), $newFilename);
                    $user->setAvatarUrl('/uploads/avatars/'.$newFilename);
                } else {
                    $this->addFlash('error', "Vous n'avez pas uploadé d'avatar !");

                    return $this->redirectToRoute('app_register');
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $mailerService->sendActivationEmail($user->getEmail(), $token);

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Veuillez vérifier votre email pour l’activer.');

            return $this->redirectToRoute('app_login');
        }// end if

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
     * @param string                      $token          le token d'activation
     * @param Request                     $request        la requête
     *                                                    HTTP
     * @param EntityManagerInterface      $em             le gestionnaire
     *                                                    d'entités
     * @param UserPasswordHasherInterface $passwordHasher le service de hachage de mots de passe
     *
     * @return Response la réponse HTTP
     */
    #[Route('/activate/{token}', name: 'app_activate_account', methods: ['GET', 'POST'])]
    public function showActivationForm(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $user = $em->getRepository(User::class)->findOneBy(['activationToken' => $token]);

        if (null === $user) {
            $this->addFlash('error', 'Token invalide.');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ActivationFormType::class);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
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
            $em->flush();

            $this->addFlash('success', 'Votre compte est maintenant actif !');

            return $this->redirectToRoute('app_login');
        }// end if

        return $this->render(
            'registration/activate.html.twig',
            [
                'activationForm' => $form->createView(),
                'token'          => $token,
            ]
        );
    }// end showActivationForm()


}// end class
