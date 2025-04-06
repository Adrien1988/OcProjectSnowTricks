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
     * EDIT - route de modification (ou création) d’une figure.
     *
     * @param Request                $request La requête HTTP
     * @param EntityManagerInterface $em      Le gestionnaire d'entités Doctrine
     * @param Figure|null            $figure  La figure à modifier (ou null si création)
     *
     * @return Response|RedirectResponse
     */
    #[Route('/figure/add', name: 'app_figure_add', methods: ['GET', 'POST'])]
    #[Route('/figure/{slug}/edit', name: 'app_figure_edit', methods: ['GET', 'POST'])]
    public function editFigure(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, ?Figure $figure = null): Response
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

        if ($form->isSubmitted()) {
            $existingFigure = $em->getRepository(Figure::class)->findOneBy(['name' => $figure->getName()]);

            if ($existingFigure && (!$figure->getId() || $existingFigure->getId() !== $figure->getId())) {
                return $this->render('partials/_create_figure_form.html.twig', [
                    'createFigureForm' => $form->createView(),
                ]);
            }

            if ($form->isValid()) {
                if (!$figure->getSlug()) {
                    $figure->generateSlug($slugger);
                }

                $em->persist($figure);
                $em->flush();

                return new Response('<div id="create-figure-success" data-redirect="'.$this->generateUrl('app_figure_detail', ['slug' => $figure->getSlug()]).'"></div>');
            }

            // retour du HTML partiel de la modale
            return $this->render('partials/_create_figure_form.html.twig', [
                'createFigureForm' => $form->createView(),
            ]);
        }

        // Formulaires secondaires (uniquement en édition)
        $mainImageForm = $isEdit ? $this->createForm(MainImageType::class, null, ['figure' => $figure])->createView() : null;
        $imageForms = [];
        $videoForms = [];

        if ($isEdit) {
            foreach ($figure->getImages() as $img) {
                $imageForms[$img->getId()] = $this->createForm(ImageType::class, $img)->createView();
            }

            foreach ($figure->getVideos() as $vid) {
                $videoForms[$vid->getId()] = $this->createForm(VideoType::class, $vid)->createView();
            }
        }

        return $this->render(
            $isEdit ? 'figure/edit.html.twig' : 'home/index.html.twig',
            [
                'form'             => $form->createView(),
                'figure'           => $figure,
                'mainImageForm'    => $mainImageForm,
                'imageForms'       => $imageForms,
                'videoForms'       => $videoForms,
                'editMode'         => $isEdit,
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
    #[Route('/figure/{slug}', name: 'app_figure_detail', methods: ['GET'])]
    public function detail(
        EntityManagerInterface $em,
        string $slug,
        CommentRepository $commentRepository,
        Request $request,
    ): Response {

        $figure = $em->getRepository(Figure::class)->findOneBy(['slug' => $slug]);
        // Vérifier si le slug fourni correspond à celui de la figure
        if (!$figure) {
            throw $this->createNotFoundException("Figure introuvable pour le slug : $slug");
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
