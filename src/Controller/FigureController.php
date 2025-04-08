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
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureController extends AbstractController
{


    /**
     * Gère la création ou la modification d’une figure.
     *
     * @param Request                $request La requête HTTP contenant le formulaire
     * @param EntityManagerInterface $em      Le gestionnaire Doctrine pour la persistance
     * @param SluggerInterface       $slugger Le service utilisé pour générer les slugs
     * @param Figure|null            $figure  La figure à modifier (null en création)
     *
     * @return Response La réponse HTML partielle (AJAX) ou complète (page d'édition)
     */
    #[Route('/figure/add', name: 'app_figure_add', methods: ['GET', 'POST'])]
    #[Route('/figure/{slug}/edit', name: 'app_figure_edit', methods: ['GET', 'POST'])]
    public function editFigure(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, ?Figure $figure = null): Response
    {
        $isEdit = $this->isEditMode($figure);
        $figure = $this->initializeFigure($figure);

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($this->isDuplicateFigureName($em, $figure)) {
                return $this->render('partials/_create_figure_form.html.twig', [
                    'createFigureForm' => $form->createView(),
                ]);
            }

            if ($form->isValid()) {
                $this->saveFigure($em, $figure, $slugger);

                return new Response('<div id="create-figure-success" data-redirect="'.$this->generateUrl('app_figure_detail', ['slug' => $figure->getSlug()]).'"></div>');
            }

            return $this->render('partials/_create_figure_form.html.twig', [
                'createFigureForm' => $form->createView(),
            ]);
        }

        // Formulaires secondaires (uniquement en édition)
        return $this->render(
            $isEdit ? 'figure/edit.html.twig' : 'home/index.html.twig',
            [
                'form'             => $form->createView(),
                'figure'           => $figure,
                'mainImageForm'    => $isEdit ? $this->createForm(MainImageType::class, null, ['figure' => $figure])->createView() : null,
                'imageForms'       => $isEdit ? $this->generateImageForms($figure) : [],
                'videoForms'       => $isEdit ? $this->generateVideoForms($figure) : [],
                'editMode'         => $isEdit,
            ]
        );
    }


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


    #[Route('/figure/{slug}', name: 'app_figure_detail', methods: ['GET'])]
    public function detail(EntityManagerInterface $em, string $slug, CommentRepository $commentRepository, Request $request): Response
    {
        $figure = $em->getRepository(Figure::class)->findOneBy(['slug' => $slug]);

        if (!$figure) {
            throw $this->createNotFoundException("Figure introuvable pour le slug : $slug");
        }

        $mainImageForm = $this->createForm(MainImageType::class, null, ['figure' => $figure]);
        $commentsData = $commentRepository->findByFigureWithPagination($figure?->getId(), $request->query->getInt('page', 1), 10);

        return $this->render('figure/detail.html.twig', [
            'figure'        => $figure,
            'comments'      => $commentsData['items'] ?? [],
            'currentPage'   => $commentsData['currentPage'] ?? 1,
            'lastPage'      => $commentsData['lastPage'] ?? 1,
            'imageForm'     => $this->createForm(ImageType::class)->createView(),
            'videoForm'     => $this->createForm(VideoType::class)->createView(),
            'commentForm'   => $this->createForm(CommentType::class)->createView(),
            'mainImageForm' => $mainImageForm->createView(),
        ]);
    }


    // ----------------------
    // Helpers privés
    // ----------------------

    private function isEditMode(?Figure $figure): bool
    {
        return $figure && $figure->getId();
    }


    private function initializeFigure(?Figure $figure): Figure
    {
        if (!$figure) {
            $figure = new Figure();
            $figure->setAuthor($this->getUser());
        } else {
            $this->denyAccessUnlessGranted('FIGURE_EDIT', $figure);
        }

        return $figure;
    }


    private function isDuplicateFigureName(EntityManagerInterface $em, Figure $figure): bool
    {
        $existingFigure = $em->getRepository(Figure::class)->findOneBy(['name' => $figure->getName()]);

        return $existingFigure && (!$figure->getId() || $existingFigure->getId() !== $figure->getId());
    }


    private function saveFigure(EntityManagerInterface $em, Figure $figure, SluggerInterface $slugger): void
    {
        if (!$figure->getSlug()) {
            $figure->generateSlug($slugger);
        }

        $em->persist($figure);
        $em->flush();
    }


    private function generateImageForms(Figure $figure): array
    {
        $forms = [];
        foreach ($figure->getImages() as $img) {
            $forms[$img->getId()] = $this->createForm(ImageType::class, $img)->createView();
        }

        return $forms;
    }


    private function generateVideoForms(Figure $figure): array
    {
        $forms = [];
        foreach ($figure->getVideos() as $vid) {
            $forms[$vid->getId()] = $this->createForm(VideoType::class, $vid)->createView();
        }

        return $forms;
    }


}
