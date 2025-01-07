<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ResetPasswordController extends AbstractController
{


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

        return $this->render('forgot_password/reset.html.twig', ['form' => $form->createView()]);

    }


}
