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
    }// end login()


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
    }// end logout()


}
