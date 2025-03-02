<?php

namespace App\Controller;

use App\Service\EntityService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur abstrait pour gérer le CRUD d’une entité (create/edit/delete).
 * Les contrôleurs concrets doivent hériter de cette classe et
 * implémenter au minimum getEntityClass() et getFormType().
 *
 * NB : Les actions “non-CRUD” (ex: show/detail) restent dans le
 * contrôleur concret car elles sont trop spécifiques.
 */
abstract class AbstractCrudController extends BaseController
{


    /**
     * Constructeur du contrôleur XXX (adapter le nom si besoin).
     *
     * @param EntityService $entityService Service générique pour la gestion des entités
     *
     * @return void
     */
    public function __construct(protected EntityService $entityService)
    {
    }


    /**
     * Retourne le nom de la classe de l'entité associée.
     *
     * @return string le nom de la classe de l'entité
     */
    abstract protected function getEntityClass(): string;


    /**
     * Retourne le nom de la classe du formulaire associé.
     *
     * @return string le nom de la classe du formulaire
     */
    abstract protected function getFormType(): string;


    /**
     * Crée une nouvelle instance de l'entité.
     *
     * Cette méthode peut être surchargée pour personnaliser la création d'une entité.
     *
     * @return object une nouvelle instance de l'entité
     */
    protected function createNewEntity(): object
    {
        $class = $this->getEntityClass();

        return new $class();
    }


    /**
     * Exécuté après la validation du formulaire pour effectuer des actions spécifiques avant la sauvegarde.
     *
     * Peut être surchargé pour ajouter du traitement (ex : upload de fichiers).
     *
     * @param object        $entity  L'entité en cours
     *                               de modification
     * @param Request       $request la requête
     *                               HTTP
     * @param FormInterface $form    le formulaire
     *                               validé
     *
     * @return void
     */
    protected function onFormSuccess(object $entity, Request $request, FormInterface $form): void
    {
        // Par défaut : rien, on surcharge dans le contrôleur concret si besoin
    }


    /**
     * Redirige après une création réussie.
     *
     * Cette méthode peut être surchargée pour personnaliser la redirection après la création d'une entité.
     *
     * @param object $entity L'entité nouvellement créée
     *
     * @return RedirectResponse la réponse de redirection
     */
    protected function redirectAfterCreate(object $entity): RedirectResponse
    {
        // Par défaut, on va sur app_home (à adapter selon ton besoin)
        return $this->redirectToRoute('app_home');
    }


    /**
     * Redirige après une mise à jour réussie.
     *
     * Cette méthode peut être surchargée pour personnaliser la redirection après la modification d'une entité.
     *
     * @param object $entity L'entité mise à jour
     *
     * @return RedirectResponse la réponse de redirection
     */
    protected function redirectAfterUpdate(object $entity): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }


    /**
     * Redirige après une suppression réussie.
     *
     * Cette méthode peut être surchargée pour personnaliser la redirection après la suppression d'une entité.
     *
     * @param object $entity L'entité supprimée
     *
     * @return RedirectResponse la réponse de redirection
     */
    protected function redirectAfterDelete(object $entity): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }


    /**
     * Affiche le formulaire de création si nécessaire.
     *
     * Cette méthode doit être surchargée dans les sous-classes pour personnaliser l'affichage.
     * Par défaut, une exception est levée pour forcer l'implémentation.
     *
     * @param object $entity L'entité à créer
     * @param mixed  $form   le formulaire
     *                       associé
     *
     * @throws \LogicException si la méthode n'est pas surchargée
     *
     * @return mixed doit être implémenté dans les classes filles pour retourner une réponse valide
     */
    protected function renderCreateForm($entity, $form)
    {
        throw new \LogicException(__METHOD__.' not implemented in '.static::class);
    }


    /**
     * Affiche la page d'édition d'une entité.
     *
     * Cette méthode doit être surchargée si un template spécifique est nécessaire,
     * notamment si l'édition inclut des sous-formulaires ou une logique spécifique.
     *
     * @param object $entity L'entité à éditer
     * @param mixed  $form   le formulaire associé
     *                       à l'entité
     *
     * @throws \LogicException si la méthode n'est pas surchargée
     *
     * @return mixed doit être implémenté dans les classes filles pour retourner une réponse valide
     */
    protected function renderEditForm($entity, $form)
    {
        throw new \LogicException(__METHOD__.' not implemented in '.static::class);
    }


    // ------------------------------------------------------------------
    //                          ACTIONS CRUD
    // ------------------------------------------------------------------


    /**
     * Gère la création d'une entité.
     *
     * Cette méthode initialise une nouvelle entité, génère le formulaire associé,
     * traite la soumission et enregistre l'entité en base de données.
     * Elle redirige ensuite vers la page définie après la création.
     *
     * @param Request $request    la requête
     *                            HTTP
     * @param string  $successMsg le message de succès affiché après la création (par défaut : "Création
     *                            réussie")
     * @param string  $failRoute  la route vers laquelle rediriger en cas d'échec (par défaut
     *                            : "app_home")
     * @param array   $failParams les paramètres supplémentaires pour la redirection en cas
     *                            d'échec
     *
     * @return mixed une réponse HTTP, soit un formulaire affiché en cas d'échec, soit une redirection après la création
     */
    public function createAction(Request $request, string $successMsg = 'Création réussie', string $failRoute = 'app_home', array $failParams = []): mixed
    {
        $entity = $this->createNewEntity();
        $form = $this->createForm($this->getFormType(), $entity);

        // handleFormSubmission => skipRedirectOnSuccess => true
        $response = $this->handleFormSubmission($request, $form, $successMsg, $failRoute, $failParams, true, true);
        // 2) Cas particulier : le formulaire n’est pas soumis => on affiche la vue
        if ($response === 'render') {
            return $this->renderCreateForm($entity, $form);
        }

        // 3) S’il y a une redirection (erreur ou autre), on la renvoie
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        // 4) Sinon, le formulaire est valide => on exécute la logique custom
        $this->onFormSuccess($entity, $request, $form);

        $this->entityService->saveEntity($entity);

        return $this->redirectAfterCreate($entity);
    }


    /**
     * Gère l'édition d'une entité existante.
     *
     * Cette méthode récupère une entité à partir de son identifiant, génère le formulaire associé,
     * traite la soumission et met à jour l'entité en base de données.
     * Elle redirige ensuite vers la page définie après la mise à jour.
     *
     * @param int     $id         L'identifiant de l'entité
     *                            à modifier
     * @param Request $request    la requête
     *                            HTTP
     * @param string  $successMsg le message de succès affiché après la mise à jour (par défaut : "Mise à jour
     *                            réussie")
     * @param string  $failRoute  la route vers laquelle rediriger en cas d'échec (par défaut
     *                            : "app_home")
     * @param array   $failParams les paramètres supplémentaires pour la redirection en cas
     *                            d'échec
     *
     * @return mixed une réponse HTTP, soit un formulaire affiché en cas d'échec, soit une redirection après la mise à jour
     */
    public function editAction(int $id, Request $request, string $successMsg = 'Mise à jour réussie', string $failRoute = 'app_home', array $failParams = []): mixed
    {
        $entity = $this->entityService->findEntityById($this->getEntityClass(), $id);
        if (!$entity) {
            $this->addFlash('error', 'Entité introuvable.');

            return $this->redirectToRoute($failRoute, $failParams);
        }

        $form = $this->createForm($this->getFormType(), $entity);

        // skipRedirectOnSuccess => true, returnRenderIfNotSubmitted => true
        $response = $this->handleFormSubmission($request, $form, $successMsg, $failRoute, $failParams, true, true);
        if ($response === 'render') {
            // on appelle la surcharge "renderEditForm"
            return $this->renderEditForm($entity, $form);
        }

        if ($response instanceof RedirectResponse) {
            return $response;
        }

        // Form valide => hook perso
        $this->onFormSuccess($entity, $request, $form);

        $this->entityService->saveEntity($entity);

        return $this->redirectAfterUpdate($entity);
    }


    /**
     * Gère la suppression d'une entité.
     *
     * Cette méthode recherche une entité par son identifiant, vérifie le jeton CSRF,
     * supprime l'entité si tout est valide et redirige ensuite vers la page définie.
     *
     * @param int     $id         L'identifiant de l'entité
     *                            à supprimer
     * @param Request $request    la requête
     *                            HTTP
     * @param string  $tokenName  le préfixe du nom du jeton CSRF (par défaut
     *                            : "delete_entity_")
     * @param string  $successMsg le message de succès affiché après la suppression (par défaut : "Supprimé avec
     *                            succès")
     * @param string  $failRoute  la route vers laquelle rediriger en cas d'échec (par défaut
     *                            : "app_home")
     * @param array   $failParams les paramètres supplémentaires pour la redirection en cas
     *                            d'échec
     *
     * @return mixed une réponse HTTP, soit une redirection après suppression, soit un message d'erreur
     */
    public function deleteAction(int $id, Request $request, string $tokenName = 'delete_entity_', string $successMsg = 'Supprimé avec succès', string $failRoute = 'app_home', array $failParams = []): mixed
    {
        $entity = $this->entityService->findEntityById($this->getEntityClass(), $id);
        if (!$entity) {
            $this->addFlash('error', 'Entité introuvable.');

            return $this->redirectToRoute($failRoute, $failParams);
        }

        if (!$this->isCsrfTokenValid($tokenName.$id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute($failRoute, $failParams);
        }

        // Vérifier si l'entité possède une méthode getMainImage (cas de Figure)
        if (method_exists($entity, 'getMainImage') && $entity->getMainImage() !== null) {
            $entity->setMainImage(null);
            // On sauvegarde cette modification (flush de la dissociation)
            $this->entityService->saveEntity($entity);
        }

        $ok = $this->entityService->saveEntity($entity, true);
        if ($ok) {
            $this->addFlash('success', $successMsg);

            return $this->redirectAfterDelete($entity);
        }

        $this->addFlash('error', 'Echec de la suppression.');
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute($failRoute, $failParams);
    }


}
