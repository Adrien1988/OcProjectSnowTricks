<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Entity\Video;
use App\Form\FigureType;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class FigureController extends AbstractController
{


    /**
     * Affiche la page de détails d'une figure.
     *
     * @param string           $slug             slug de la figure
     * @param FigureRepository $figureRepository repository pour accéder aux figures
     *
     * @return Response la réponse HTTP avec le rendu de la page
     */
    #[Route('/figure/{slug}', name: 'app_figure_detail', methods: ['GET'])]
    public function detail(string $slug, FigureRepository $figureRepository): Response
    {
        // Récupération de la figure avec ses images et vidéos via le repository
        $figure = $figureRepository->findOneWithRelations($slug);

        if (!$figure) {
            throw $this->createNotFoundException('La figure demandée n\'existe pas.');
        }

        // Création des formulaires pour les images et vidéos
        $imageForm = $this->createForm(ImageType::class);
        $videoForm = $this->createForm(VideoType::class);

        return $this->render(
            'figure/detail.html.twig',
            [
                'figure'    => $figure,
                'imageForm' => $imageForm->createView(),
                'videoForm' => $videoForm->createView(),
            ]
        );
    }


    /**
     * Charge plus de commentaires via AJAX.
     *
     * @param string           $slug             slug de la figure
     * @param FigureRepository $figureRepository repository pour accéder aux figures
     *
     * @return JsonResponse les commentaires supplémentaires
     */
    #[Route('/figure/{slug}/comments', name: 'app_figure_load_comments', methods: ['GET'])]
    public function loadComments(string $slug, FigureRepository $figureRepository): JsonResponse
    {
        $figure = $figureRepository->findOneBy(['slug' => $slug]);

        if (!$figure) {
            return new JsonResponse(['error' => 'Figure introuvable'], 404);
        }

        // Retourne tous les commentaires sous forme de JSON
        $comments = $figure->getComments();

        return new JsonResponse(
            [
                'comments' => array_map(
                    function ($comment) {
                        return [
                            'author'    => $comment->getAuthor()->getUsername(),
                            'createdAt' => $comment->getCreatedAt()->format('d/m/Y H:i'),
                            'content'   => $comment->getContent(),
                        ];
                    },
                    $comments->toArray()
                ),
            ]
        );
    }


    /**
     * Modifie une figure existante.
     *
     * Affiche un formulaire pré-rempli avec les données de la figure et sauvegarde les modifications.
     *
     * @param int                    $id               identifiant de la figure à modifier
     * @param Request                $request          requête HTTP contenant les données du formulaire
     * @param EntityManagerInterface $entityManager    gestionnaire d'entités pour sauvegarder les données
     * @param FigureRepository       $figureRepository repository pour accéder aux figures
     *
     * @return Response retourne la réponse HTTP
     */
    #[Route('/figure/edit/{id}', name: 'app_figure_edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository,
    ): Response {

        // Vérifie que l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Récupère la figure ou renvoie une erreur 404
        $figure = $figureRepository->find($id);
        if (!$figure) {
            throw new NotFoundHttpException('La figure demandée n\'existe pas.');
        }

        // Pré-remplit le formulaire avec les données de la figure
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Ajoute un message flash et redirige
            $this->addFlash('success', 'La figure a été modifiée avec succès.');

            return $this->redirectToRoute('home');
        }

        return $this->render(
            'figure/edit.html.twig',
            [
                'form'   => $form->createView(),
                'figure' => $figure,
            ]
        );
    }


    /**
     * Ajoute une vidéo à une figure.
     *
     * @param Figure                 $figure        L'entité de la figure
     * @param Request                $request       La requête HTTP
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
     *
     * @return Response
     */
    #[Route('/figure/{id}/add-video', name: 'app_figure_add_video', methods: ['POST'])]
    public function addVideo(Figure $figure, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Initialisation de l'entité Video et du formulaire
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        // Vérification si le formulaire a été soumis
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Le formulaire de vidéo contient des erreurs.');

            return $this->redirectToRoute('app_figure_detail', ['slug' => $figure->getSlug()]);
        }

        // Association de la vidéo avec la figure
        $video->setFigure($figure);

        // Sauvegarde dans la base de données
        $entityManager->persist($video);
        $entityManager->flush();

        $this->addFlash('success', 'La vidéo a été ajoutée avec succès.');

        // Redirection vers la page de détail
        return $this->redirectToRoute('app_figure_detail', ['slug' => $figure->getSlug()]);
    }


    /**
     * Ajoute une image à une figure.
     *
     * @param Figure                 $figure        La figure associée
     *                                              à l'image
     * @param Request                $request       La requête HTTP contenant les données
     *                                              du formulaire
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
     *
     * @return Response
     */
    #[Route('/figure/{id}/add-image', name: 'app_figure_add_image', methods: ['POST'])]
    public function addImage(Figure $figure, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Initialisation de l'entité Image et du formulaire
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs.');

            return $this->redirectToRoute('app_figure_detail', ['slug' => $figure->getSlug()]);
        }

        // Récupération du fichier uploadé
        $uploadedFile = $form->get('file')->getData();
        if (!$uploadedFile) {
            $this->addFlash('error', 'Aucun fichier sélectionné.');

            return $this->redirectToRoute('app_figure_detail', ['slug' => $figure->getSlug()]);
        }

        // Gestion de l'upload du fichier
        $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
        try {
            $uploadedFile->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );

            // Mise à jour des propriétés de l'entité Image
            $image->setUrl('/uploads/'.$newFilename);
            $image->setFigure($figure);

            // Persistance de l'entité dans la base de données
            $entityManager->persist($image);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'L\'image a été ajoutée avec succès.');
        } catch (FileException $e) {
            // Gestion des erreurs lors de l'upload
            $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
        }

        // Redirection vers la page de détail de la figure
        return $this->redirectToRoute('app_figure_detail', ['slug' => $figure->getSlug()]);
    }


    /**
     * Supprime une figure existante.
     *
     * Cette méthode permet de supprimer une figure de la base de données après
     * une confirmation et une validation CSRF.
     *
     * @param int                    $id               identifiant de la figure à supprimer
     * @param EntityManagerInterface $entityManager    gestionnaire d'entités pour la suppression
     * @param FigureRepository       $figureRepository repository pour accéder aux figures
     * @param Request                $request          requête HTTP contenant le token CSRF
     *
     * @return RedirectResponse redirige vers la liste des figures avec un message
     */
    #[Route('/figure/delete/{id}', name: 'app_figure_delete', methods: ['POST'])]
    public function delete(
        int $id,
        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository,
        Request $request,
    ): RedirectResponse {

        // Vérifie que l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Récupère la figure ou renvoie une erreur 404 si elle n'existe pas
        $figure = $figureRepository->find($id);
        if (!$figure) {
            $this->addFlash('danger', 'La figure demandée n\'existe pas.');

            return $this->redirectToRoute('home');
        }

        // Vérifie le token CSRF
        if (!$this->isCsrfTokenValid('delete_figure_'.$figure->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');

            return $this->redirectToRoute('home');
        }

        // Supprime la figure
        $entityManager->remove($figure);
        $entityManager->flush();

        $this->addFlash('success', 'La figure a été supprimée avec succès.');

        return $this->redirectToRoute('home');
    }


}
