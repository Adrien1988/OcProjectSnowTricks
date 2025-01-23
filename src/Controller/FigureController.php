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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $figure = $this->findFigureBySlug($slug, $figureRepository);

        return $this->render(
            'figure/detail.html.twig',
            [
                'figure'    => $figure,
                'imageForm' => $this->createForm(ImageType::class)->createView(),
                'videoForm' => $this->createForm(VideoType::class)->createView(),
            ]
        );
    }


    /**
     * Charge plus de commentaires via AJAX.
     *
     * @param string           $slug             Le slug de la figure
     * @param FigureRepository $figureRepository Le repository pour accéder aux figures
     *
     * @return JsonResponse Les commentaires supplémentaires en format JSON
     */
    #[Route('/figure/{slug}/comments', name: 'app_figure_load_comments', methods: ['GET'])]
    public function loadComments(string $slug, FigureRepository $figureRepository): JsonResponse
    {
        $figure = $figureRepository->findOneBy(['slug' => $slug]);

        if (!$figure) {
            return new JsonResponse(['error' => 'Figure introuvable'], 404);
        }

        $comments = $figure->getComments();

        return new JsonResponse(
            [
                'comments' => array_map(
                    fn ($comment) => [
                        'author'    => $comment->getAuthor()->getUsername(),
                        'createdAt' => $comment->getCreatedAt()->format('d/m/Y H:i'),
                        'content'   => $comment->getContent(),
                    ],
                    $comments->toArray()
                ),
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
     * @return RedirectResponse La redirection vers la page de détails
     */
    #[Route('/figure/{id}/add-video', name: 'app_figure_add_video', methods: ['POST'])]
    public function addVideo(Figure $figure, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(VideoType::class, $video = new Video());
        $form->handleRequest($request);

        if ($this->handleFormErrors($form, 'Le formulaire de vidéo contient des erreurs.', $figure)) {
            return $this->redirectToFigureDetail($figure);
        }

        if (!$this->isEmbedCodeValid($video->getEmbedCode())) {
            $this->addFlash('error', 'Le code d\'intégration n\'est pas valide.');

            return $this->redirectToFigureDetail($figure);
        }

        $video->setFigure($figure);
        $this->saveEntity($entityManager, $video, 'La vidéo a été ajoutée avec succès.');

        return $this->redirectToFigureDetail($figure);
    }


    /**
     * Ajoute une image à une figure.
     *
     * @param Figure                 $figure        La figure associée à l'image
     * @param Request                $request       La requête HTTP contenant les données du formulaire
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
     *
     * @return RedirectResponse La redirection vers la page de détails
     */
    #[Route('/figure/{id}/add-image', name: 'app_figure_add_image', methods: ['POST'])]
    public function addImage(Figure $figure, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ImageType::class, $image = new Image());
        $form->handleRequest($request);

        if ($this->handleFormErrors($form, 'Le formulaire de l\'image contient des erreurs.', $figure)) {
            return $this->redirectToFigureDetail($figure);
        }

        $uploadedFile = $form->get('file')->getData();
        if (!$uploadedFile) {
            $this->addFlash('error', 'Aucun fichier sélectionné.');

            return $this->redirectToFigureDetail($figure);
        }

        $newFilename = $this->uploadFile($uploadedFile, 'uploads_directory');
        if (!$newFilename) {
            $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');

            return $this->redirectToFigureDetail($figure);
        }

        $image->setUrl('/uploads/'.$newFilename);
        $image->setFigure($figure);
        $this->saveEntity($entityManager, $image, 'L\'image a été ajoutée avec succès.');

        return $this->redirectToFigureDetail($figure);
    }


    /**
     * Modifie une figure existante.
     *
     * @param int                    $id               L'identifiant de la figure à modifier
     * @param Request                $request          La requête HTTP contenant les données
     * @param EntityManagerInterface $entityManager    Le gestionnaire d'entités
     * @param FigureRepository       $figureRepository Le repository pour accéder aux figures
     *
     * @return Response La réponse HTTP avec le formulaire de modification
     */
    #[Route('/figure/edit/{id}', name: 'app_figure_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, FigureRepository $figureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $figureRepository->find($id);
        if (!$figure) {
            throw $this->createNotFoundException('La figure demandée n\'existe pas.');
        }

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
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
     * Supprime une figure existante.
     *
     * @param int                    $id               L'identifiant de la figure à supprimer
     * @param EntityManagerInterface $entityManager    Le gestionnaire d'entités
     * @param FigureRepository       $figureRepository Le repository pour accéder aux figures
     * @param Request                $request          La requête HTTP contenant le token CSRF
     *
     * @return RedirectResponse La redirection vers la liste des figures
     */
    #[Route('/figure/delete/{id}', name: 'app_figure_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager, FigureRepository $figureRepository, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $figureRepository->find($id);
        if (!$figure || !$this->isCsrfTokenValid('delete_figure_'.$figure->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide ou figure introuvable.');

            return $this->redirectToRoute('home');
        }

        $entityManager->remove($figure);
        $entityManager->flush();
        $this->addFlash('success', 'La figure a été supprimée avec succès.');

        return $this->redirectToRoute('home');
    }


    /**
     * Trouve une figure par son slug ou lance une exception.
     *
     * @param string           $slug             Le slug de la figure
     * @param FigureRepository $figureRepository Le repository pour accéder aux figures
     *
     * @return Figure La figure trouvée
     */
    private function findFigureBySlug(string $slug, FigureRepository $figureRepository): Figure
    {
        $figure = $figureRepository->findOneWithRelations($slug);

        if (!$figure) {
            throw $this->createNotFoundException('La figure demandée n\'existe pas.');
        }

        return $figure;
    }


    /**
     * Gère les erreurs de formulaire.
     *
     * @param mixed  $form         Le formulaire à
     *                             vérifier
     * @param string $errorMessage Le message d'erreur à
     *                             afficher
     * @param Figure $figure       La figure liée au
     *                             formulaire
     *
     * @return bool Renvoie true si le formulaire contient des erreurs
     */
    private function handleFormErrors($form, string $errorMessage, Figure $figure): bool
    {
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', $errorMessage);

            return true;
        }

        return false;
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


    /**
     * Sauvegarde une entité.
     *
     * @param EntityManagerInterface $entityManager  Le gestionnaire d'entités
     * @param object                 $entity         L'entité à sauvegarder
     * @param string                 $successMessage Le message de succès à afficher
     *
     * @return void
     */
    private function saveEntity(EntityManagerInterface $entityManager, object $entity, string $successMessage): void
    {
        try {
            $entityManager->persist($entity);
            $entityManager->flush();
            $this->addFlash('success', $successMessage);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la sauvegarde.');
        }
    }


    /**
     * Redirige vers les détails de la figure.
     *
     * @param Figure $figure La figure vers laquelle rediriger
     *
     * @return RedirectResponse La redirection vers la page de détail
     */
    private function redirectToFigureDetail(Figure $figure): RedirectResponse
    {
        return $this->redirectToRoute('app_figure_detail', ['slug' => $figure->getSlug()]);
    }


    /**
     * Gère l'upload d'un fichier.
     *
     * @param mixed  $file      Le fichier uploadé
     * @param string $parameter Le paramètre définissant le répertoire cible
     *
     * @return string|null Le nom du fichier généré ou null en cas d'erreur
     */
    private function uploadFile($file, string $parameter): ?string
    {
        $newFilename = uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getParameter($parameter), $newFilename);

            return $newFilename;
        } catch (\Exception $e) {
            return null;
        }
    }


}
