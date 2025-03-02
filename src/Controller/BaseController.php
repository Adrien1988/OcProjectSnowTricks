<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{


    /**
     * Gère la soumission et la validation des formulaires avec gestion des messages flash.
     *
     * @param Request $request                    La requête HTTP contenant les données du formulaire
     * @param mixed   $form                       L'instance du formulaire à traiter
     * @param string  $successMessage             Message en cas de succès
     * @param string  $redirectRoute              Route de redirection en cas d'erreur ou après succès
     * @param array   $routeParams                Paramètres de la route de redirection
     * @param bool    $skipRedirectOnSuccess      Si true, ne redirige pas après un succès (renvoie null)
     * @param bool    $returnRenderIfNotSubmitted Si true, renvoie la chaîne 'render' si le form n'est pas soumis
     *
     * @return RedirectResponse|string|null retourne:
     *                                      - 'render' si le formulaire n'est pas soumis et qu'on veut
     *                                      quand même afficher la vue courante,
     *                                      - une RedirectResponse si on doit rediriger (erreurs ou succès),
     *                                      - null si on n'a pas redirection (ex: succès, mais skipRedirectOnSuccess = true)
     */
    public function handleFormSubmission(
        Request $request,
        $form,
        string $successMsg,
        string $redirRoute,
        array $params = [],
        bool $skipRedir = false,
        bool $retRender = false
    ): RedirectResponse|string|null {
        $form->handleRequest($request);
    
        // Si le formulaire n'est pas soumis, on retourne 'render' ou null
        if (!$form->isSubmitted()) {
            return $retRender ? 'render' : null;
        }
    
        // Si le formulaire est soumis et valide, on affiche le message et on redirige si nécessaire
        if ($form->isValid()) {
            $this->addFlash('success', $successMsg);
            if ($skipRedir) {
                return null;
            }
            return $this->redirectToRoute($redirRoute, $params);
        }
    
        // Si le formulaire est soumis mais non valide, on collecte et affiche les erreurs
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        if (!empty($errors)) {
            $this->addFlash('error', 'Veuillez corriger les erreurs : ' . implode(' - ', $errors));
        }
    
        return $this->redirectToRoute($redirRoute, $params);
    }
    


    /**
     * Gère la réponse après la soumission du formulaire.
     *
     * Cette méthode traite les retours de `handleFormSubmission()` :
     * - Si la réponse est "render", elle affiche le formulaire.
     * - Si la réponse est une `RedirectResponse`, elle effectue la redirection.
     * - Si aucun traitement n'est nécessaire, elle retourne `null`.
     *
     * @param mixed  $response   La réponse retournée par `handleFormSubmission()`
     * @param string $template   Le template Twig à afficher si nécessaire
     * @param array  $parameters Les paramètres à passer à la vue
     *
     * @return Response|null Retourne une `Response` pour affichage ou redirection, ou `null` si aucune action n'est requise
     */
    protected function handleFormResponse(
        mixed $response,
        string $template,
        array $parameters = [],
    ): ?Response {
        // 🔹 Si handleFormSubmission retourne "render", on affiche le formulaire
        if ($response === 'render') {
            return $this->render($template, $parameters);
        }

        // 🔹 Si handleFormSubmission retourne une redirection, on la suit
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        // 🔹 Si aucun traitement n'a été effectué, on retourne null (permet de continuer le processus dans le contrôleur)
        return null;
    }


}
