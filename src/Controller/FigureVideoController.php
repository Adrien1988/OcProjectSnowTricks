<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Video;
use App\Form\VideoType;
use App\Service\EntityService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figure/video')]
class FigureVideoController extends AbstractCrudController
{


    /**
     * Constructeur du contrôleur FigureVideoController.
     *
     * @param EntityService $entityService Service pour la gestion des entités
     */
    public function __construct(
        protected EntityService $entityService,
    ) {
        parent::__construct($entityService);
    }


    /**
     * Retourne le nom de la classe entièrement qualifiée (FQCN) de l'entité manipulée.
     *
     * @return string Le nom de la classe complète de l'entité (ex. : Video::class).
     */
    protected function getEntityClass(): string
    {
        return Video::class;
    }


    /**
     * Retourne le nom de la classe entièrement qualifiée (FQCN) du formulaire associé à l'entité.
     *
     * @return string Le nom de la classe complète du formulaire (ex. : VideoType::class).
     */
    protected function getFormType(): string
    {
        return VideoType::class;
    }


    /**
     * Hook invoqué après validation du formulaire mais avant l'enregistrement en base.
     *
     * Cette méthode permet d'ajouter une logique spécifique, comme l'association d'une figure
     * ou la validation du code d'intégration de la vidéo.
     *
     * @param object        $entity  L'entité Video en cours de traitement
     * @param Request       $request la requête HTTP
     * @param FormInterface $form    le formulaire validé
     *
     * @throws \RuntimeException si le code d'intégration de la vidéo est invalide
     *
     * @return void
     */
    protected function onFormSuccess(object $entity, Request $request, FormInterface $form): void
    {
        /*
         * @var Video $video
         */

        $video = $entity;

        $figureId = $request->attributes->get('figureId');
        if ($figureId) {
            $figure = $this->entityService->findEntityById(Figure::class, $figureId);
            if ($figure) {
                $video->setFigure($figure);
            }
        }

        if (!$this->isEmbedCodeValid($video->getEmbedCode())) {
            throw new \RuntimeException("Le code d'intégration de la vidéo n'est pas valide.");
        }
    }


    /**
     * Redirige après la création réussie d'une vidéo.
     *
     * Après l'ajout d'une vidéo, cette méthode redirige vers la page de détail de la figure associée
     * au lieu de la page d'accueil.
     *
     * @param object $entity L'entité nouvellement créée (ici, une Video)
     *
     * @return RedirectResponse la réponse de redirection après la création
     */
    protected function redirectAfterCreate(object $entity): RedirectResponse
    {
        /*
         * @var Video $video
         */

        $video = $entity;
        $figure = $video->getFigure();

        // Si jamais la figure est introuvable, on redirige vers l'accueil
        if (!$figure) {
            return $this->redirectToRoute('app_home');
        }

        // Sinon, on retourne sur la page de détail de la figure
        return $this->redirectToRoute(
            'app_figure_detail',
            [
                'id' => $figure->getId(),
            ]
        );
    }


    /**
     * Affiche le formulaire de création d'une vidéo.
     *
     * @param object        $entity la vidéo à créer
     * @param FormInterface $form   le formulaire associé
     *
     * @return Response la réponse contenant le rendu du formulaire
     */
    protected function renderCreateForm($entity, $form)
    {
        throw new \LogicException("Pas de vue GET pour la création d'une vidéo dans ".__CLASS__);
    }


    /**
     * Affiche le formulaire d'édition d'une vidéo.
     *
     * @param object        $entity la vidéo à éditer
     * @param FormInterface $form   le formulaire associé
     *
     * @return Response la réponse contenant le rendu du formulaire
     */
    protected function renderEditForm($entity, $form): Response
    {

        return $this->render(
            'video/edit.html.twig',
            [
                'form'  => $form->createView(),
                'video' => $entity,
            ]
        );
    }


    // ------------------------------------------------------------------
    //               ROUTES UTILISANT L'ABSTRACT
    // ------------------------------------------------------------------


    /**
     * Ajoute une vidéo à une figure.
     *
     * @param int     $id      L'identifiant de la figure associée
     * @param Request $request la requête HTTP
     *
     * @return Response|RedirectResponse la réponse après ajout (affichage du formulaire ou redirection)
     */
    #[Route('/add/{id}', name: 'app_figure_add_video', methods: ['GET', 'POST'])]
    public function addVideo(int $id, Request $request): Response|RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $request->attributes->set('figureId', $id);

        return $this->createAction(
            $request,
            'Vidéo ajoutée avec succès.',
            'app_figure_detail',
            ['id' => $id]
        );
    }


    /**
     * Modifie une vidéo existante.
     *
     * @param int     $id      L'identifiant de la vidéo à modifier
     * @param Request $request la requête HTTP
     *
     * @return Response|RedirectResponse la réponse après modification (affichage du formulaire ou redirection)
     */
    #[Route('/edit/{id}', name: 'app_figure_edit_video', methods: ['GET', 'POST'])]
    public function editVideo(int $id, Request $request): Response|RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $video = $this->entityService->findEntityById(Video::class, $id);
        if (!$video) {
            $this->addFlash('error', 'Vidéo introuvable.');

            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGranted('VIDEO_EDIT', $video);

        return $this->editAction(
            $id,
            $request,
            'Vidéo mise à jour avec succès.',
            'app_figure_edit',
            ['id' => $video->getFigure()->getId()]
        );
    }


    /**
     * Supprime une vidéo existante.
     *
     * @param int     $id      L'identifiant de la vidéo à supprimer
     * @param Request $request la requête HTTP
     *
     * @return Response|RedirectResponse la réponse après suppression (redirection ou message d'erreur)
     */
    #[Route('/delete/{id}', name: 'app_figure_delete_video', methods: ['POST'])]
    public function deleteVideo(int $id, Request $request): Response|RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $video = $this->entityService->findEntityById(Video::class, $id);
        if (!$video) {
            $this->addFlash('error', 'Vidéo introuvable.');

            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGranted('VIDEO_DELETE', $video);

        return $this->deleteAction(
            $id,
            $request,
            'delete_video_',
            'Vidéo supprimée avec succès',
            'app_figure_edit',
            ['id' => $video->getFigure()->getId()]
        );
    }


    /**
     * Vérifie si le code d'intégration de la vidéo est valide.
     * (Méthode interne, utilisée par onFormSuccess()).
     *
     * @param string|null $embedCode Code HTML d'iframe
     *
     * @return bool
     */
    private function isEmbedCodeValid(?string $embedCode): bool
    {
        return $embedCode && preg_match('/<iframe.*>.*<\/iframe>/', $embedCode);
    }


}
