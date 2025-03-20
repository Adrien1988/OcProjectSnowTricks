<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Form\ImageType;
use App\Form\MainImageType;
use App\Form\VideoType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{


    /**
     * EDIT - route de modification (ou création) d’une figure.
     *
     * @param Request                $request La requête HTTP
     * @param EntityManagerInterface $em      Le gestionnaire d'entités Doctrine
     * @param Figure|null            $figure  La figure à modifier (ou null si création)
     *
     * @return Response|RedirectResponse
     */
    #[Route('/figure/add', name: 'app_figure_add', methods: ['GET', 'POST'])]
    #[Route('/figure/edit/{id}', name: 'app_figure_edit', methods: ['GET', 'POST'])]
    public function editFigure(Request $request, EntityManagerInterface $em, ?Figure $figure = null): Response
    {
        $isEdit = ($figure && $figure->getId());

        if (!$figure) {
            $figure = new Figure();
            $figure->setAuthor($this->getUser());
        } else {
            $this->denyAccessUnlessGranted('FIGURE_EDIT', $figure);
        }

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($figure);
            $em->flush();

            $this->addFlash('success', $isEdit ? 'Figure modifiée.' : 'Figure créée.');

            return $this->redirectToRoute(
                'app_figure_detail',
                [
                    'id'   => $figure->getId(),
                    'slug' => $figure->getSlug(),
                ]
            );
        }

        // Formulaires secondaires (image principale, images, vidéos)
        $mainImageForm = $this->createForm(MainImageType::class, null, ['figure' => $figure])->createView();

        $imageForms = [];
        foreach ($figure->getImages() as $img) {
            $imageForms[$img->getId()] = $this->createForm(ImageType::class, $img)->createView();
        }

        $videoForms = [];
        foreach ($figure->getVideos() as $vid) {
            $videoForms[$vid->getId()] = $this->createForm(VideoType::class, $vid)->createView();
        }

        return $this->render(
            'figure/edit.html.twig',
            [
                'form'          => $form->createView(),
                'figure'        => $figure,
                'mainImageForm' => $mainImageForm,
                'imageForms'    => $imageForms,
                'videoForms'    => $videoForms,
                'editMode'      => $isEdit,
            ]
        );
    }


    /**
     * DELETE - route de suppression d’une figure.
     *
     * @param Figure                 $figure  La figure à supprimer
     * @param Request                $request La requête HTTP (contenant le token CSRF)
     * @param EntityManagerInterface $em      Le gestionnaire d'entités Doctrine
     *
     * @return RedirectResponse
     */
    #[Route('/figure/delete/{id}', name: 'app_figure_delete', methods: ['POST'])]
    public function delete(Figure $figure, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $this->denyAccessUnlessGranted('FIGURE_DELETE', $figure);

        if ($this->isCsrfTokenValid('delete_figure_'.$figure->getId(), $request->request->get('_token'))) {
            if ($figure->getMainImage()) {
                $figure->setMainImage(null);
                $em->flush();
            }

            $em->remove($figure);
            $em->flush();

            $this->addFlash('success', 'Figure supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Erreur de sécurité : impossible de supprimer la figure.');
        }

        return $this->redirectToRoute('app_home');
    }


    /**
     * Affiche la page de détails d'une figure (non-CRUD).
     *
     * @param Figure            $figure            La figure dont on affiche le détail
     * @param string            $slug              Le slug de la figure
     * @param CommentRepository $commentRepository Le repository pour paginer les commentaires
     * @param Request           $request           La requête HTTP
     *
     * @return Response La réponse contenant le rendu de la page de détail
     */
    #[Route('/figure/{id}/{slug}', name: 'app_figure_detail', methods: ['GET'])]
    public function detail(
        Figure $figure,
        string $slug,
        CommentRepository $commentRepository,
        Request $request,
    ): Response {

        // Vérifier si le slug fourni correspond à celui de la figure
        if ($figure->getSlug() !== $slug) {
            // Redirection 301 vers l'URL "canonique"
            return $this->redirectToRoute(
                'app_figure_detail',
                [
                    'id'   => $figure->getId(),
                    'slug' => $figure->getSlug(),
                ],
                301
            );
        }

        // Formulaire de modification de l'image principale
        $mainImageForm = $this->createForm(MainImageType::class, null, ['figure' => $figure]);

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
