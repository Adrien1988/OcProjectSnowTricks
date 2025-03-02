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
     * @param Request $request    la requête HTTP contenant les données du formulaire
     * @param mixed   $form       L'instance du formulaire à traiter
     * @param string  $successMsg message affiché en cas de succès
     * @param string  $redirRoute route de redirection en cas d'erreur ou après succès
     * @param array   $params     paramètres supplémentaires pour la route de redirection
     * @param bool    $skipRedir  si true, ne redirige pas après un succès (renvoie null)
     * @param bool    $retRender  si true, renvoie la chaîne 'render' si le formulaire n'est pas soumis
     *
     * @return RedirectResponse|string|null retourne :
     *                                      - 'render' si le formulaire n'est pas soumis et que l'on souhaite afficher la vue courante,
     *                                      - une RedirectResponse si une redirection est requise (erreurs ou succès),
     *                                      - null si aucune redirection n'est nécessaire (ex : succès, mais $skipRedir = true)
     */
    public function handleFormSubmission(
        Request $request,
        $form,
        string $successMsg,
        string $redirRoute,
        array $params = [],
        bool $skipRedir = false,
        bool $retRender = false,
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
            $this->addFlash('error', 'Veuillez corriger les erreurs : '.implode(' - ', $errors));
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
