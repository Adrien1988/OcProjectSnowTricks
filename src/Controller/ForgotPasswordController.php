<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ForgotPasswordController extends AbstractController
{


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

                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token], true);

                $emailMessage = (new Email())
                    ->from('no-reply@example.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html('<p>Cliquez sur ce lien pour réinitialiser votre mot de passe :</p><a href="'.$resetUrl.'">'.$resetUrl.'</a>');

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Si un compte existe pour cet email, un lien de réinitialisation a été envoyé.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'forgot_password/request.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


}
