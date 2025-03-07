<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Form\CommentType;
use App\Service\EntityService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractCrudController
{


    /**
     * Constructeur du CommentController.
     *
     * @param EntityService $entityService Service pour gérer les entités
     *
     * @return void
     */
    public function __construct(
        protected EntityService $entityService,
    ) {
        parent::__construct($entityService);
    }


    /**
     * Renvoie le Fully Qualified Class Name (FQCN)
     * de l'entité Comment.
     *
     * @return string Le FQCN de Comment (par exemple "App\Entity\Comment")
     */
    protected function getEntityClass(): string
    {
        return Comment::class;
    }


    /**
     * Renvoie le Fully Qualified Class Name (FQCN)
     * du formulaire CommentType.
     *
     * @return string Le FQCN du formulaire, par exemple "App\Form\CommentType"
     */
    protected function getFormType(): string
    {
        return CommentType::class;
    }


    /**
     * Hook pour ajouter la logique métier après validation du form,
     * avant la sauvegarde en base (association Figure, auteur, etc.).
     *
     * @param object        $entity  L'entité (ici, Comment)
     * @param Request       $request La requête HTTP
     * @param FormInterface $form    Le formulaire validé
     *
     * @return void
     */
    protected function onFormSuccess(object $entity, Request $request, FormInterface $form): void
    {
        /*
         * @var Comment $comment
         */

        $comment = $entity;

        $figureId = $request->attributes->get('figureId');
        if ($figureId) {
            $figure = $this->entityService->findEntityById(Figure::class, $figureId);
            if ($figure) {
                $comment->setFigure($figure);
            }
        }

        $comment->setAuthor($this->getUser());
    }


    /**
     * Surcharge de redirectAfterCreate() pour rediriger vers la page détail
     * de la figure associée après la création d'un commentaire.
     *
     * @param object $entity L'entité Comment nouvellement créée
     *
     * @return RedirectResponse
     */
    protected function redirectAfterCreate(object $entity): RedirectResponse
    {
        /*
         * @var Comment $comment
         */

        $comment = $entity;
        $figure = $comment->getFigure();

        if (!$figure) {
            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_figure_detail', ['id' => $figure->getId(), 'slug' => $figure->getSlug()]);
    }


    /**
     * Surcharge si besoin d'une vue de création GET.
     * Ici, on ne l'a pas dans l'exemple => on lève une exception.
     *
     * @param object $entity L'entité Comment
     * @param mixed  $form   Le formulaire
     *
     * @throws \LogicException
     *
     * @return never
     */
    protected function renderCreateForm($entity, $form)
    {
        throw new \LogicException('Aucune vue GET pour la création de commentaire dans '.__CLASS__);
    }


    // --------------------------------------------------------------------
    //                          ROUTES
    // --------------------------------------------------------------------


    /**
     * Ajoute un commentaire à une figure (POST).
     * Utilise la logique de createAction() de l'abstract.
     *
     * @param int     $id      L'identifiant de la figure
     * @param Request $request La requête HTTP contenant le formulaire
     *
     * @return RedirectResponse|Response
     */
    #[Route('/figure/{id}/add-comment', name: 'app_figure_add_comment', methods: ['POST'])]
    public function addComment(int $id, Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $request->attributes->set('figureId', $id);

        return $this->createAction(
            $request,
            'Commentaire ajouté avec succès.',
            'app_figure_detail',
            ['id' => $id]
        );
    }


}
