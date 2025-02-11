<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur de sécurité pour gérer la connexion et la déconnexion des utilisateurs.
 */
class SecurityController extends AbstractController
{


    /**
     * Gère la page de connexion.
     *
     * @param AuthenticationUtils $authenticationUtils service pour récupérer les erreurs et le dernier nom d'utilisateur saisi
     *
     * @return Response la réponse HTTP contenant le formulaire de connexion
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    } // end login()


    /**
     * Gère la déconnexion de l'utilisateur.
     *
     * Cette méthode ne contient pas de logique car elle est interceptée par le firewall de Symfony.
     *
     * @throws \LogicException cette exception est levée par défaut car la méthode ne doit pas être appelée directement
     *
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    } // end logout()


    /**
     * Gère la demande de réinitialisation de mot de passe.
     *
     * @param Request                $request       La requête HTTP actuelle
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour interagir avec la base de données
     * @param MailerInterface        $mailer        Le service d'envoi d'emails
     *
     * @return Response La réponse HTTP contenant la page de demande de réinitialisation
     */
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt(new \DateTimeImmutable('+1 hour'));

                $entityManager->flush();

                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $emailMessage = (new Email())
                    ->from('no-reply@example.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html(
                        '<p>Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :</p>
                    <p><a href="' . $resetUrl . '" style="display: inline-block; padding: 10px 15px; background-color: #007bff; 
                        color: white; text-decoration: none; border-radius: 5px;">Réinitialiser mon mot de passe</a></p>'
                    );

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Si un compte existe pour cet email, un lien de réinitialisation a été envoyé.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/forgot_password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Gère la réinitialisation du mot de passe.
     *
     * @param Request                     $request        La requête HTTP actuelle
     * @param string                      $token          Le token de réinitialisation envoyé par email
     * @param EntityManagerInterface      $entityManager  Le gestionnaire d'entités pour interagir avec la base de données
     * @param UserPasswordHasherInterface $passwordHasher Le service de hachage des mots de passe
     *
     * @return Response La réponse HTTP contenant la page de réinitialisation ou une redirection
     */
    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, string $token, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user || !$user->isResetTokenValid()) {
            $this->addFlash('error', 'Le token est invalide ou expiré.');

            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);

            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', ['form' => $form->createView()]);
    }


}
