<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Video;
use App\Form\VideoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figure/video')]
class FigureVideoController extends AbstractController
{


    /**
     * Crée ou modifie une vidéo pour une figure.
     *
     * Routes :
     *  - "/figure/video/add/{figureId}" => name="app_figure_add_video" (création)
     *  - "/figure/video/edit/{id}"      => name="app_figure_edit_video" (édition)
     *
     * @param Request                $request  La requête HTTP
     * @param EntityManagerInterface $em       Le gestionnaire d'entités Doctrine
     * @param int|null               $figureId L'ID de la figure (pour la création), ou null si édition
     * @param Video|null             $video    La vidéo à modifier (null si création)
     *
     * @return Response|RedirectResponse
     */
    #[Route('/add/{figureId}', name: 'app_figure_add_video', methods: ['GET', 'POST'])]
    #[Route('/edit/{id}', name: 'app_figure_edit_video', methods: ['GET', 'POST'])]
    public function formVideo(Request $request, EntityManagerInterface $em, ?int $figureId = null, ?Video $video = null): Response|RedirectResponse
    {
        $isEdit = ($video && $video->getId());

        if (!$isEdit) {
            $video = new Video();
            // Associer la vidéo à la figure si figureId existe
            if ($figureId) {
                $figure = $em->getRepository(Figure::class)->find($figureId);
                if (!$figure) {
                    $this->addFlash('error', 'Figure introuvable pour associer une vidéo.');

                    return $this->redirectToRoute('app_home');
                }

                $video->setFigure($figure);
            }

            $this->denyAccessUnlessGranted('VIDEO_CREATE', $video);
        } else {
            // Cas édition => contrôle des droits via le Voter
            $this->denyAccessUnlessGranted('VIDEO_EDIT', $video);
        }

        // Création et traitement du formulaire
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification du code d'intégration
            if (!$this->isEmbedCodeValid($video->getEmbedCode())) {
                $this->addFlash('error', 'Le code d’intégration de la vidéo est invalide.');

                // On réaffiche le formulaire
                return $this->render(
                    'video/form.html.twig',
                    [
                        'form'   => $form->createView(),
                        'video'  => $video,
                        'isEdit' => $isEdit,
                    ]
                );
            }

            // Persistance en base
            $em->persist($video);
            $em->flush();

            $this->addFlash(
                'success',
                $isEdit ? 'La vidéo a été modifiée avec succès.' : 'La vidéo a été ajoutée avec succès.'
            );

            // Redirection
            $figure = $video->getFigure();
            if ($figure) {
                $route = $isEdit ? 'app_figure_edit' : 'app_figure_detail';

                return $this->redirectToRoute(
                    $route,
                    [
                        'slug' => $figure->getSlug(),
                    ]
                );
            }

            return $this->redirectToRoute('app_home');
        }

        // Affichage du formulaire
        return $this->render(
            'video/form.html.twig',
            [
                'form'   => $form->createView(),
                'video'  => $video,
                'isEdit' => $isEdit,
            ]
        );
    }


    /**
     * Supprime une vidéo existante.
     *
     * @param Video                  $video   La vidéo à supprimer
     * @param Request                $request La requête HTTP
     * @param EntityManagerInterface $em      Le gestionnaire d'entités Doctrine
     *
     * @return RedirectResponse
     */
    #[Route('/delete/{id}', name: 'app_figure_delete_video', methods: ['POST'])]
    public function deleteVideo(Video $video, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $this->denyAccessUnlessGranted('VIDEO_DELETE', $video);

        // Vérification token CSRF
        if (!$this->isCsrfTokenValid('delete_video_'.$video->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_home');
        }

        // Suppression en base
        $figure = $video->getFigure();
        $em->remove($video);
        $em->flush();

        $this->addFlash('success', 'Vidéo supprimée avec succès.');

        // Redirection vers la figure ou home
        if ($figure) {
            return $this->redirectToRoute(
                'app_figure_edit',
                [
                    'slug' => $figure->getSlug(),
                ]
            );
        }

        return $this->redirectToRoute('app_home');

    }


    /**
     * Vérifie si le code d'intégration de la vidéo est valide.
     * (Recherche d'une balise <iframe>).
     *
     * @param string|null $embedCode Le code HTML d'iframe
     *
     * @return bool
     */
    private function isEmbedCodeValid(?string $embedCode): bool
    {
        if (!$embedCode) {
            return false;
        }

        // Vérifie simplement la présence d'une balise <iframe ...></iframe>
        return (bool) preg_match('/<iframe.*<\/iframe>/', $embedCode);

    }


}
