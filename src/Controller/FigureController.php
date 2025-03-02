<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Form\ImageType;
use App\Form\MainImageType;
use App\Form\VideoType;
use App\Repository\CommentRepository;
use App\Service\EntityService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureController extends AbstractCrudController
{


    /**
     * Constructeur du contrôleur FigureController.
     *
     * @param EntityService    $entityService Service pour la gestion générique des entités
     * @param SluggerInterface $slugger       Service pour générer le slug
     *
     * @return void
     */
    public function __construct(
        protected EntityService $entityService,
        private SluggerInterface $slugger,
    ) {
        parent::__construct($entityService);
    }


    /**
     * Retourne le nom de la classe entièrement qualifiée (FQCN) de l'entité manipulée par ce contrôleur.
     *
     * Cette méthode doit être surchargée dans les sous-classes pour spécifier l'entité concernée.
     *
     * @return string Le nom de la classe complète de l'entité (ex. : Figure::class).
     */
    protected function getEntityClass(): string
    {
        return Figure::class;
    }


    /**
     * Retourne le nom de la classe entièrement qualifiée (FQCN) du formulaire associé à l'entité Figure.
     *
     * Cette méthode doit être surchargée dans les sous-classes si un autre formulaire est requis.
     *
     * @return string Le nom de la classe complète du formulaire (ex. : FigureType::class).
     */
    protected function getFormType(): string
    {
        return FigureType::class;
    }


    /**
     * Personnalise l'instanciation de l'entité avant la phase standard de création.
     *
     * Cette méthode peut être surchargée pour ajouter des valeurs par défaut à l'entité,
     * comme l'affectation de l'auteur.
     *
     * @return object une nouvelle instance de l'entité Figure
     */
    protected function createNewEntity(): object
    {
        $figure = new Figure();
        $figure->setAuthor($this->getUser());

        return $figure;
    }


    /**
     * Exécute une logique additionnelle après la création d'une entité avant la redirection finale.
     *
     * Cette méthode peut être surchargée pour effectuer des actions spécifiques après la création,
     * comme la génération d'un slug ou d'autres traitements nécessaires.
     *
     * @param object $entity L'entité nouvellement créée
     *
     * @return RedirectResponse la réponse de redirection après la création
     */
    protected function redirectAfterCreate(object $entity): RedirectResponse
    {
        /*
         * @var Figure $figure
         */

        $figure = $entity;

        // On génère le slug après validation du formulaire
        // et avant la sauvegarde finale (création).
        // Si vous préférez, on peut le faire dans createNewEntity(),
        // mais souvent le slug dépend de champs saisis dans le formulaire.
        $figure->generateSlug($this->slugger);
        // On sauvegarde maintenant l'entité modifiée avec son slug.
        $this->entityService->saveEntity($figure);

        // Redirection finale (par défaut, on retourne sur la page d'accueil)
        return $this->redirectToRoute('app_home');
    }


    /**
     * Exécute une logique additionnelle après la mise à jour d'une entité avant la redirection finale.
     *
     * Cette méthode peut être surchargée pour effectuer des actions spécifiques après la modification,
     * comme la régénération d'un slug ou d'autres traitements nécessaires.
     *
     * @param object $entity L'entité mise à jour
     *
     * @return RedirectResponse la réponse de redirection après la mise à jour
     */
    protected function redirectAfterUpdate(object $entity): RedirectResponse
    {
        /*
         * @var Figure $figure
         */

        $figure = $entity;

        // On redirige vers la page de détail après la mise à jour
        return $this->redirectToRoute('app_figure_detail', ['id' => $figure->getId()]);
    }


    /**
     * Exécute une logique additionnelle après la suppression d'une entité avant la redirection finale.
     *
     * Cette méthode peut être surchargée pour effectuer des actions spécifiques après la suppression,
     * comme la suppression de fichiers liés ou la mise à jour d'autres entités.
     *
     * @param object $entity L'entité supprimée
     *
     * @return RedirectResponse la réponse de redirection après la suppression
     */
    protected function redirectAfterDelete(object $entity): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }


    /**
     * Méthode pour construire et afficher le formulaire d'édition
     * (page d'édition complète, formulaires d'images, vidéos, etc.).
     *
     * @param object $entity Instance à éditer (ici, un Figure)
     * @param mixed  $form   Formulaire principal (FigureType)
     *
     * @return Response
     */
    protected function renderEditForm($entity, $form)
    {
        /*
         * @var Figure $figure
         */

        $figure = $entity;

        // Formulaire pour changer l'image principale
        $mainImageForm = $this->createForm(
            MainImageType::class,
            null,
            [
                'figure' => $figure,
            ]
        )->createView();

        // Formulaires d'édition pour chaque image
        $imageForms = [];
        foreach ($figure->getImages() as $img) {
            $imageForms[$img->getId()] = $this->createForm(ImageType::class, $img)->createView();
        }

        // Formulaires d'édition pour chaque vidéo
        $videoForms = [];
        foreach ($figure->getVideos() as $vid) {
            $videoForms[$vid->getId()] = $this->createForm(VideoType::class, $vid)->createView();
        }

        return $this->render(
            'figure/edit.html.twig',
            [
                'form'           => $form->createView(),
                'figure'         => $figure,
                'mainImageForm'  => $mainImageForm,
                'imageForms'     => $imageForms,
                'videoForms'     => $videoForms,
            ]
        );
    }


    // ------------------------------------------------------------------
    //                       ROUTES CRUD
    // ------------------------------------------------------------------


    /**
     * CREATE - route d’ajout d’une nouvelle figure.
     *
     * @param Request $request La requête HTTP
     *
     * @return Response|RedirectResponse
     */
    #[Route('/figure/add', name: 'app_figure_add', methods: ['GET', 'POST'])]
    public function create(Request $request): mixed
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Appel à la logique générique de création
        return $this->createAction($request, 'Figure créée avec succès', 'app_home');
    }


    /**
     * EDIT - route de modification d’une figure existante.
     *
     * @param int     $id      L’identifiant de la figure à modifier
     * @param Request $request La requête HTTP
     *
     * @return Response|RedirectResponse
     */
    #[Route('/figure/edit/{id}', name: 'app_figure_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): mixed
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $this->entityService->findEntityById($this->getEntityClass(), $id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGranted('FIGURE_EDIT', $figure);

        // Appel à la logique générique de mise à jour
        return $this->editAction($id, $request, 'Figure modifiée avec succès', 'app_figure_detail', ['id' => $id]);
    }


    /**
     * DELETE - route de suppression d’une figure.
     *
     * @param int     $id      L’identifiant de la figure à supprimer
     * @param Request $request La requête contenant le token CSRF
     *
     * @return RedirectResponse
     */
    #[Route('/figure/delete/{id}', name: 'app_figure_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): mixed
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $this->entityService->findEntityById($this->getEntityClass(), $id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGranted('FIGURE_DELETE', $figure);

        // Appel à la logique générique de suppression
        return $this->deleteAction($id, $request, 'delete_figure_', 'Figure supprimée avec succès', 'app_home');
    }


    /**
     * Affiche la page de détails d'une figure (non-CRUD).
     *
     * @param int               $id                Identifiant de la figure
     * @param EntityService     $entityService     Service pour la gestion des entités
     * @param CommentRepository $commentRepository Accès aux commentaires
     * @param Request           $request           Requête HTTP
     *
     * @return Response
     */
    #[Route('/figure/{id}', name: 'app_figure_detail', methods: ['GET'])]
    public function detail(
        int $id,
        EntityService $entityService,
        CommentRepository $commentRepository,
        Request $request,
    ): Response {
        $figure = $entityService->findEntityById(Figure::class, $id);

        // Formulaire de modification de l'image principale
        $mainImageForm = $this->createForm(MainImageType::class, null, ['figure' => $figure]);
        $mainImageForm->handleRequest($request);

        if ($mainImageForm->isSubmitted() && $mainImageForm->isValid()) {
            $selectedImageId = $mainImageForm->get('mainImage')->getData();
            $selectedImage = array_filter($figure->getImages(), fn ($img) => $img->getId() == $selectedImageId)[0] ?? null;

            if ($selectedImage && $entityService->saveEntity($figure->setMainImage($selectedImage))) {
                $this->addFlash('success', 'Image principale mise à jour.');

                return $this->redirectToRoute('app_figure_detail', ['id' => $figure->getId()]);
            }

            $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de l’image principale.');
        }

        if ($mainImageForm->isSubmitted() && !$mainImageForm->isValid()) {
            $errors = array_map(fn ($error) => $error->getMessage(), iterator_to_array($mainImageForm->getErrors(true)));
            if ($errors) {
                $this->addFlash('error', 'Veuillez corriger les erreurs pour l’image principale : '.implode(' - ', $errors));
            }
        }

        // Gestion de la pagination des commentaires
        $commentsData = $commentRepository->findByFigureWithPagination($figure?->getId(), $request->query->getInt('page', 1), 10);

        return $this->render(
            'figure/detail.html.twig',
            [
                'figure'        => $figure,
                'comments'      => $commentsData['items'] ?? [],
                'currentPage'   => $commentsData['currentPage'] ?? 1,
                'lastPage'      => $commentsData['lastPage'] ?? 1,
                'imageForm'     => $this->createForm(ImageType::class)->createView(),
                'videoForm'     => $this->createForm(VideoType::class)->createView(),
                'commentForm'   => $this->createForm(CommentType::class)->createView(),
                'mainImageForm' => $mainImageForm->createView(),
            ]
        );
    }


}
