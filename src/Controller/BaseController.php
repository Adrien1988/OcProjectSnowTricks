<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
    protected function handleFormSubmission(
        Request $request,
        $form,
        string $successMessage,
        string $redirectRoute,
        array $routeParams = [],
        bool $skipRedirectOnSuccess = false,
        bool $returnRenderIfNotSubmitted = false,
    ): RedirectResponse|string|null {
        $form->handleRequest($request);

        // 🔹 Si le formulaire n'est pas soumis, on ne redirige PAS (évite la boucle infinie)
        if (!$form->isSubmitted()) {
            return $returnRenderIfNotSubmitted ? 'render' : null;
        }

        // Si le formulaire est soumis et valide
        if ($form->isValid()) {
            $this->addFlash('success', $successMessage);

            // Si on veut éviter la redirection immédiate, on retourne null
            if ($skipRedirectOnSuccess) {
                return null;
            }

            return $this->redirectToRoute($redirectRoute, $routeParams);
        }

        // Gestion des erreurs si le formulaire est soumis mais non valide
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        if (!empty($errors)) {
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire : '.implode(' - ', $errors));
        }

        return $this->redirectToRoute($redirectRoute, $routeParams);

    }


}
