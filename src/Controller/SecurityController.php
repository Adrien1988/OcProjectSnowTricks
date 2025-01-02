<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant les actions liées à la sécurité, comme la connexion et la déconnexion.
 */
class SecurityController extends AbstractController
{


    /**
     * Affiche le formulaire de connexion et traite les erreurs d'authentification.
     *
     * @param AuthenticationUtils $authenticationUtils outil pour gérer les erreurs d'authentification et le dernier identifiant saisi
     *
     * @return Response la réponse contenant la vue du formulaire de connexion
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupérer les erreurs d'authentification et le dernier identifiant saisi.
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]
        );

    }// end login()


    /**
     * Déconnecte l'utilisateur.
     *
     * Cette méthode est interceptée automatiquement par Symfony et ne sera jamais exécutée.
     *
     * @throws \LogicException exception levée si la méthode est appelée directement
     *
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode est interceptée par la route app_logout.');
    }// end logout()


}// end class
