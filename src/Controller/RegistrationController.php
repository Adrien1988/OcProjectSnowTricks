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

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'unicité de l'email et du username.
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');

                return $this->redirectToRoute('app_register');
            }

            // Hashage du mot de passe.
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            // Activer l'utilisateur directement (ou gérer l'activation par email).
            $user->setIsActive(false);

            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);

            $avatarMethod = $form->get('avatarMethod')->getData();

            if ('url' === $avatarMethod) {
                $avatarUrl = $form->get('avatarUrl')->getData();
                if ($avatarUrl) {
                    $user->setAvatarUrl($avatarUrl);
                }
            }

            if ('upload' === $avatarMethod) {
                $avatarFile = $form->get('avatarFile')->getData();
                if ($avatarFile) {
                    $newFilename = uniqid().'.'.$avatarFile->guessExtension();
                    // Déplacement du fichier vers un répertoire défini dans services.yaml.
                    $avatarFile->move($this->getParameter('avatars_directory'), $newFilename);
                    $user->setAvatarUrl('/uploads/avatars/'.$newFilename);
                } else {
                    // Gérer le cas où aucun fichier n'est uploadé alors que l'utilisateur a choisi "upload".
                    $this->addFlash('error', "Vous n'avez pas uploadé d'avatar !");

                    return $this->redirectToRoute('app_register');
                }
            }

            // Enregistrer l'utilisateur.
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer l'email d'activation.
            $mailerService->sendActivationEmail($user->getEmail(), $token);

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Veuillez vérifier votre email pour l’activer.');

            return $this->redirectToRoute('app_login');
        }//end if

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form->createView(),
            ]
        );

    }//end register()


    #[Route('/activate/{token}', name: 'app_activate_account', methods: ['GET', 'POST'])]
    public function showActivationForm(string $token, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['activationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token invalide.');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ActivationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data            = $form->getData();
            $email           = $data['email'];
            $enteredPassword = $data['password'];

            // Vérifier que l'email saisi correspond à celui de l'utilisateur.
            if ($email !== $user->getEmail()) {
                $this->addFlash('error', 'L’email ne correspond pas à celui associé au compte.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            // Vérifier le mot de passe.
            // Comme le compte n'est pas actif, mais a déjà un mot de passe haché,
            // on peut vérifier que l'utilisateur connaît ce mot de passe.
            if (!$passwordHasher->isPasswordValid($user, $enteredPassword)) {
                $this->addFlash('error', 'Le mot de passe est incorrect.');

                return $this->redirectToRoute('app_activate_account', ['token' => $token]);
            }

            // Si tout est bon, activer le compte.
            $user->setIsActive(true);
            $user->setActivationToken(null);
            $em->flush();

            $this->addFlash('success', 'Votre compte est maintenant actif !');

            return $this->redirectToRoute('app_login');
        }//end if

        return $this->render(
            'registration/activate.html.twig',
            [
                'activationForm' => $form->createView(),
                'token'          => $token,
            ]
        );

    }//end showActivationForm()


}//end class
