<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BaseController extends AbstractController
{
    /**
     * Gère la soumission et la validation des formulaires.
     *
     * @param Request $request
     * @param mixed   $form
     * @param string  $redirectRoute
     * @param array   $routeParams
     *
     * @return RedirectResponse
     */
    protected function handleFormSubmission(Request $request, $form, string $redirectRoute, array $routeParams = []): RedirectResponse
    {
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute($redirectRoute, $routeParams);
        }

        // Gestion des erreurs si le formulaire est soumis mais non valide
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire : '.implode(' - ', $errors));
            }
        }

        return $this->redirectToRoute($redirectRoute, $routeParams);
    }
}
