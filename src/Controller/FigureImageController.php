<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Form\ImageType;
use App\Form\MainImageType;
use App\Service\EntityService;
use App\Service\FileUploader;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figure/image')]
class FigureImageController extends AbstractCrudController
{


    /**
     * Constructeur du contrôleur FigureImageController.
     *
     * @param EntityService $entityService Service pour la gestion des entités
     * @param FileUploader  $fileUploader  Service pour l'upload/suppression de fichiers
     *
     * @return void
     */
    public function __construct(
        protected EntityService $entityService,
        private FileUploader $fileUploader,
    ) {
        parent::__construct($entityService);
    }


    /**
     * Renvoie le FQCN de l’entité manipulée par ce contrôleur (Image::class).
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Image::class;
    }


    /**
     * Renvoie le FQCN du formulaire associé à l’entité Image (ImageType::class).
     *
     * @return string
     */
    protected function getFormType(): string
    {
        return ImageType::class;
    }


    /**
     * Surcharge de createNewEntity si nécessaire.
     * Ici, on renvoie juste new Image().
     *
     * @return object
     */
    protected function createNewEntity(): object
    {
        return new Image();
    }


    /**
     * Hook exécuté après la validation du formulaire mais avant l'enregistrement en base.
     *
     * Permet d'associer l'image à une figure et de gérer l'upload du fichier.
     *
     * @param object        $entity  L'entité Image en cours de traitement
     * @param Request       $request la requête HTTP
     * @param FormInterface $form    le formulaire validé
     *
     * @return void
     */
    protected function onFormSuccess(object $entity, Request $request, FormInterface $form): void
    {
        /*
         * @var Image $image
         */

        $image = $entity;

        // Récupérer l'ID de la figure passé dans l'URL
        $figureId = $request->attributes->get('figureId');
        if ($figureId) {
            $figure = $this->entityService->findEntityById(Figure::class, $figureId);
            if ($figure) {
                $image->setFigure($figure);
            }
        }

        // Gérer l'upload si le champ file est présent
        $uploadedFile = $form->get('file')->getData();
        if ($uploadedFile) {
            $newFilename = $this->fileUploader->upload($uploadedFile);
            if ($newFilename) {
                $image->setUrl('/uploads/'.$newFilename);
            }
        }
    }


    /**
     * Redirige après la création réussie d'une image.
     *
     * Après l'ajout d'une image, cette méthode redirige vers la page de détail de la figure associée
     * au lieu de la page d'accueil.
     *
     * @param object $entity L'entité nouvellement créée (ici, une Image)
     *
     * @return RedirectResponse la réponse de redirection après la création
     */
    protected function redirectAfterCreate(object $entity): RedirectResponse
    {
        /*
         * @var Image $image
         */

        $image = $entity;
        $figure = $image->getFigure();

        if (!$figure) {
            // Par prudence : si figure est null, on redirige home ?
            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute(
            'app_figure_detail',
            [
                'id' => $figure->getId(),
            ]
        );
    }


    /**
     * Surcharge de redirectAfterUpdate() pour rediriger vers la page d'édition de la figure
     * après la mise à jour d'une image.
     *
     * @param object $entity L'entité Image modifiée
     *
     * @return RedirectResponse
     */
    protected function redirectAfterUpdate(object $entity): RedirectResponse
    {
        /*
         * @var Image $image
         */

        $image = $entity;
        $figure = $image->getFigure();
        if ($figure) {
            return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
        }

        return $this->redirectToRoute('app_home');
    }


    /**
     * Surcharge de redirectAfterDelete() pour rediriger vers la page d'édition de la figure
     * après la suppression d'une image.
     *
     * @param object $entity L'entité Image supprimée
     *
     * @return RedirectResponse
     */
    protected function redirectAfterDelete(object $entity): RedirectResponse
    {
        /*
         * @var Image $image
         */

        $image = $entity;
        $figure = $image->getFigure();
        if ($figure) {
            return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
        }

        return $this->redirectToRoute('app_home');
    }


    /**
     * Affiche le formulaire de création d'une image si nécessaire.
     *
     * Cette méthode doit être surchargée dans une classe dérivée si une vue GET est requise.
     * Par défaut, elle lève une exception car aucune page GET n'est prévue pour la création d'une image.
     *
     * @param object        $entity L'entité Image en cours de création
     * @param FormInterface $form   le formulaire associé à l'entité
     *
     * @throws \LogicException si la méthode n'est pas surchargée
     *
     * @return mixed cette méthode doit être surchargée pour retourner une réponse valide
     */
    protected function renderCreateForm($entity, $form)
    {
        throw new \LogicException("Pas de page GET pour la création d'une Image dans ".__CLASS__);
    }


    /**
     * Affiche le formulaire d'édition d'une image via une requête GET.
     *
     * Cette méthode peut être surchargée pour personnaliser l'affichage du formulaire d'édition.
     *
     * @param object        $entity L'entité Image à modifier
     * @param FormInterface $form   le formulaire associé à l'entité
     *
     * @return Response la réponse contenant le rendu du formulaire
     */
    protected function renderEditForm($entity, $form)
    {
        // Exemple minimal : renvoyer un template twig (facultatif)
        return $this->render(
            'figure_image/edit.html.twig',
            [
                'form'  => $form->createView(),
                'image' => $entity,
            ]
        );
    }


    // ------------------------------------------------------------------
    //                        ROUTES CRUD (IMAGES)
    // ------------------------------------------------------------------


    /**
     * Ajoute une image à une figure (utilise l'abstract createAction()).
     * On crée un "addImage" pour la route, puis on appelle createAction().
     *
     * @param Request $request  La requête
     * @param int     $figureId L'ID de la figure à laquelle on associe l'image
     *
     * @return Response|RedirectResponse
     */
    #[Route('/add/{figureId}', name: 'app_figure_add_image', methods: ['GET', 'POST'])]
    public function addImage(Request $request, int $figureId): Response|RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Si vous devez associer l'image à la figure dans onFormSuccess(),
        // vous pouvez passer l'id de la figure dans la Request :
        $request->attributes->set('figureId', $figureId);

        // On délègue le cycle de création (création entité, form, handleForm, etc.) au parent
        return $this->createAction(
            $request,
            "L'image a été ajoutée avec succès.",
            'app_figure_detail',
            ['id' => $figureId]
        );
    }


    /**
     * Édite une image existante (utilise l'abstract editAction()).
     *
     * @param int     $id      L'ID de l'image
     * @param Request $request La requête
     *
     * @return Response|RedirectResponse
     */
    #[Route('/edit/{id}', name: 'app_figure_edit_image', methods: ['GET', 'POST'])]
    public function editImage(int $id, Request $request): Response|RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Vérification si besoin (voter, etc.)
        $image = $this->entityService->findEntityById(Image::class, $id);
        if (!$image) {
            $this->addFlash('error', 'Image introuvable.');

            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGranted('IMAGE_EDIT', $image);

        // On appelle l'abstract => editAction()
        return $this->editAction(
            $id,
            $request,
            'Image modifiée avec succès',
            'app_figure_edit_image',
            ['id' => $id]
        );
    }


    /**
     * Supprime une image existante (utilise l'abstract deleteAction()).
     *
     * @param int     $id      L'ID de l'image
     * @param Request $request La requête
     *
     * @return RedirectResponse
     */
    #[Route('/delete/{id}', name: 'app_figure_delete_image', methods: ['POST'])]
    public function deleteImage(int $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Vérif si l'image existe et si on a le droit de supprimer
        $image = $this->entityService->findEntityById(Image::class, $id);
        if (!$image) {
            $this->addFlash('error', 'Image introuvable.');

            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGranted('IMAGE_DELETE', $image);

        // Optionnel : on supprime le fichier physique avant la suppression DB
        if (!$this->fileUploader->remove($image->getUrl())) {
            $this->addFlash('error', 'Erreur lors de la suppression du fichier image.');

            return $this->redirectToRoute('app_figure_edit', ['id' => $image->getFigure()?->getId()]);
        }

        // On confie le reste (suppression DB, redirection) à deleteAction() du parent
        return $this->deleteAction(
            $id,
            $request,
            'delete_image_',
            'Image supprimée avec succès',
            'app_figure_edit',
            ['id' => $image->getFigure()?->getId()]
        );
    }


    // ------------------------------------------------------------------
    //                  MÉTHODES SPÉCIFIQUES "FIGURE"
    // ------------------------------------------------------------------


    /**
     * Change l'image principale d'une figure.
     *
     * @param int     $id      L'identifiant de la figure
     * @param Request $request La requête HTTP contenant le formulaire
     *
     * @return RedirectResponse
     */
    #[Route('/figure/{id}/set-main-image', name: 'app_figure_set_main_image', methods: ['POST'])]
    public function setMainImage(int $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $this->entityService->findEntityById(Figure::class, $id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        // Formulaire pour désigner l'image principale
        $form = $this->createForm(MainImageType::class, null, ['figure' => $figure]);
        // Ici, on appelle handleFormSubmission() (du BaseController)
        // car ce n’est pas un "create/edit" d'entité Image,
        // mais un champ custom sur la Figure.
        $response = $this->handleFormSubmission(
            $request,
            $form,
            'Image principale mise à jour.',
            'app_figure_detail',
            ['id' => $figure->getId()],
            true,
            false
        );
        if ($response) {
            return $response;
        }

        // Récupération de l'ID de l'image choisie
        $imageId = $form->get('mainImage')->getData();
        foreach ($figure->getImages() as $img) {
            if ($img->getId() == $imageId) {
                $figure->setMainImage($img);
                break;
            }
        }

        $this->entityService->saveEntity($figure);

        // On redirige vers la page précédente (ou figure_detail)
        $referer = $request->headers->get('referer', $this->generateUrl('app_figure_detail', ['id' => $id]));

        return $this->redirect($referer);
    }


    /**
     * Supprime l'image principale d'une figure.
     *
     * @param int     $id      L'identifiant de la figure
     * @param Request $request La requête HTTP contenant le token CSRF
     *
     * @return RedirectResponse
     */
    #[Route('/figure/{id}/remove-main-image', name: 'app_figure_remove_main_image', methods: ['POST'])]
    public function removeMainImage(int $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $this->entityService->findEntityById(Figure::class, $id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        if (!$this->isCsrfTokenValid('remove_main_image_'.$figure->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_figure_detail', ['id' => $figure->getId()]);
        }

        $figure->setMainImage(null);
        $saveResult = $this->entityService->saveEntity($figure);

        $this->addFlash(
            $saveResult ? 'success' : 'error',
            $saveResult
                ? "L'image principale a été supprimée avec succès."
                : "Erreur lors de la suppression de l'image principale."
        );

        $referer = $request->headers->get('referer', $this->generateUrl('app_figure_detail', ['id' => $id]));

        return $this->redirect($referer);
    }


}
