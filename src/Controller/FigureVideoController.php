<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Video;
use App\Form\VideoType;
use App\Service\FigureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/figure/video')]
class FigureVideoController extends AbstractController
{


    /**
     * Ajoute une vidéo à une figure.
     *
     * @param Figure        $figure        L'entité de la figure
     * @param Request       $request       La requête HTTP
     * @param FigureService $figureService Service pour gérer les figures
     *
     * @return RedirectResponse La redirection vers la page de détails
     */
    #[Route('/add/{id}', name: 'app_figure_add_video', methods: ['POST'])]
    public function addVideo(Figure $figure, Request $request, FigureService $figureService): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(VideoType::class, $video = new Video());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de la validité du code d'intégration
            if (!$this->isEmbedCodeValid($video->getEmbedCode())) {
                // On ajoute un message d'erreur et on redirige si le code n'est pas valide
                $this->addFlash('error', 'Le code d\'intégration n\'est pas valide.');

                return $figureService->redirectToFigureDetail($figure);
            }

            // Si le code est valide, on associe la vidéo à la figure et on enregistre
            $video->setFigure($figure);
            if ($figureService->saveEntity($video)) {
                $this->addFlash('success', 'La vidéo a été ajoutée avec succès.');

                return $figureService->redirectToFigureDetail($figure);
            }

            $this->addFlash('error', 'Erreur lors de la sauvegarde de la vidéo.');
        }

        return $figureService->redirectToFigureDetail($figure);
    }


    /**
     * Modifie une vidéo existante.
     *
     * @param Video         $video         La vidéo à
     *                                     modifier
     * @param Request       $request       La requête HTTP contenant les données du
     *                                     formulaire
     * @param FigureService $figureService Service pour gérer les
     *                                     figures
     *
     * @return RedirectResponse La redirection vers la page d'édition de la figure
     */
    #[Route('/edit/{id}', name: 'app_figure_edit_video', methods: ['GET', 'POST'])]
    public function editVideo(
        Video $video,
        Request $request,
        FigureService $figureService,
    ): RedirectResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isEmbedCodeValid($video->getEmbedCode())) {
                $this->addFlash('error', 'Le code d\'intégration n\'est pas valide.');

                return $this->redirectToRoute('app_figure_edit', ['id' => $video->getFigure()->getId()]);
            }

            // Sauvegarde via le service
            if ($figureService->saveEntity($video)) {
                $this->addFlash('success', 'La vidéo a été mise à jour avec succès.');

                return $this->redirectToRoute('app_figure_edit', ['id' => $video->getFigure()->getId()]);
            }

            $this->addFlash('error', 'Erreur lors de la mise à jour de la vidéo.');
        }

        return $this->redirectToRoute('app_figure_edit', ['id' => $video->getFigure()->getId()]);
    }


    /**
     * Supprime une vidéo existante.
     *
     * @param Video         $video         La vidéo à supprimer
     * @param Request       $request       La requête HTTP contenant le token CSRF
     * @param FigureService $figureService Service pour gérer les figures
     *
     * @return RedirectResponse La redirection vers la page d'édition de la figure
     */
    #[Route('/delete/{id}', name: 'app_figure_delete_video', methods: ['POST'])]
    public function deleteVideo(Video $video, Request $request, FigureService $figureService): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $video->getFigure();

        if (!$this->isCsrfTokenValid('delete_video_'.$video->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
        }

        if ($figureService->saveEntity($video, true)) {
            $this->addFlash('success', 'Vidéo supprimée avec succès.');

            return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
        }

        $this->addFlash('error', 'Erreur lors de la suppression de la vidéo.');

        return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
    }


    /**
     * Vérifie si le code d'intégration de la vidéo est valide.
     *
     * @param string|null $embedCode Le code d'intégration à vérifier
     *
     * @return bool Renvoie true si le code est valide
     */
    private function isEmbedCodeValid(?string $embedCode): bool
    {
        return $embedCode && preg_match('/<iframe.*>.*<\/iframe>/', $embedCode);
    }


}
