<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Image;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{


    /**
     * Modifie une figure existante avec une interface similaire à la vue détail.
     *
     * @param int                    $id               L'identifiant de la figure à modifier
     * @param Request                $request          La requête HTTP contenant les données
     * @param EntityManagerInterface $entityManager    Le gestionnaire d'entités
     * @param FigureRepository       $figureRepository Le repository pour accéder aux figures
     *
     * @return Response La réponse HTTP avec le formulaire de modification
     */
    #[Route('/figure/edit/{id}', name: 'app_figure_edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository,
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Récupérer la figure via la méthode privée
        $figure = $this->findFigureById($id, $figureRepository);

        // Créer le formulaire de modification
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->saveEntity($entityManager, $figure, 'La figure a été modifiée avec succès.')) {
                return $this->redirectToFigureDetail($figure);
            }
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
    public function delete(
        int $id,
        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository,
        Request $request,
    ): RedirectResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Récupérer la figure
        $figure = $figureRepository->find($id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('home');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('delete_figure_'.$figure->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToFigureDetail($figure);
        }

        if ($this->saveEntity($entityManager, $figure, 'La figure a été supprimée avec succès.', true)) {
            return $this->redirectToRoute('home');
        }

        return $this->redirectToFigureDetail($figure);
    }


    /**
     * Affiche la page de détails d'une figure.
     *
     * @param string            $slug              slug de la figure
     * @param FigureRepository  $figureRepository  repository pour accéder
     *                                             aux figures
     * @param CommentRepository $commentRepository Le repository pour accéder aux commentaires
     * @param Request           $request           La
     *                                             requête
     *                                             HTTP
     *
     * @return Response la réponse HTTP avec le rendu de la page
     */
    #[Route('/figure/{slug}', name: 'app_figure_detail', methods: ['GET'])]
    public function detail(string $slug, FigureRepository $figureRepository, CommentRepository $commentRepository, Request $request): Response
    {
        $figure = $this->findFigureBySlug($slug, $figureRepository);

        // Récupération de la pagination des commentaires
        $page = $request->query->getInt('page', 1); // Par défaut, page 1
        $commentsData = $commentRepository->findByFigureWithPagination($figure->getId(), $page, 10);

        return $this->render(
            'figure/detail.html.twig',
            [
                'figure'      => $figure,
                'comments'    => $commentsData['items'],
                'currentPage' => $commentsData['currentPage'],
                'lastPage'    => $commentsData['lastPage'],
                'imageForm'   => $this->createForm(ImageType::class)->createView(),
                'videoForm'   => $this->createForm(VideoType::class)->createView(),
                'commentForm' => $this->createForm(CommentType::class)->createView(),
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

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de la validité du code d'intégration
            if (!$this->isEmbedCodeValid($video->getEmbedCode())) {
                // On ajoute un message d'erreur et on redirige si le code n'est pas valide
                $this->addFlash('error', 'Le code d\'intégration n\'est pas valide.');

                return $this->redirectToFigureDetail($figure);
            }

            // Si le code est valide, on associe la vidéo à la figure et on enregistre
            $video->setFigure($figure);
            if ($this->saveEntity($entityManager, $video, 'La vidéo a été ajoutée avec succès.')) {
                return $this->redirectToFigureDetail($figure);
            }
        }

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

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();

            // Si aucun fichier n’est transmis, on redirige immédiatement
            if (!$uploadedFile) {
                return $this->redirectToFigureDetail($figure);
            }

            // Tentative d’upload
            $newFilename = $this->uploadFile($uploadedFile, 'uploads_directory');
            if (!$newFilename) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');

                return $this->redirectToFigureDetail($figure);
            }

            // Tout est OK, on paramètre l’entité et on enregistre
            $image->setUrl('/uploads/'.$newFilename);
            $image->setFigure($figure);

            if ($this->saveEntity($entityManager, $image, 'L\'image a été ajoutée avec succès.')) {
                return $this->redirectToFigureDetail($figure);
            }

            // En cas d’échec de la sauvegarde (ex. exception en BDD), on informe l’utilisateur
            $this->addFlash('error', 'Erreur lors de la sauvegarde de l\'image.');
        }

        return $this->redirectToFigureDetail($figure);
    }


    /**
     * Ajoute un commentaire à une figure.
     *
     * Cette méthode permet aux utilisateurs connectés d'ajouter un commentaire
     * à une figure spécifique. Le commentaire est sauvegardé dans la base de données
     * et l'utilisateur est redirigé vers la page de la figure.
     *
     * @param string                 $slug             Le slug de la figure
     * @param Request                $request          La requête HTTP contenant les données du formulaire
     * @param FigureRepository       $figureRepository Le repository pour accéder aux figures
     * @param EntityManagerInterface $entityManager    Le gestionnaire d'entités pour sauvegarder les données
     *
     * @return RedirectResponse La redirection vers la page de détail de la figure
     */
    #[Route('/figure/{slug}/add-comment', name: 'app_figure_add_comment', methods: ['POST'])]
    public function addComment(string $slug, Request $request, FigureRepository $figureRepository, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $figureRepository->findOneBy(['slug' => $slug]);
        if (!$figure) {
            throw $this->createNotFoundException('Figure introuvable.');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setFigure($figure);

            if ($this->saveEntity($entityManager, $comment, 'Votre commentaire a été ajouté avec succès.')) {
                return $this->redirectToFigureDetail($figure);
            }
        }

        return $this->redirectToFigureDetail($figure);

    }


    /**
     * Récupère et affiche les commentaires d'une figure avec pagination.
     *
     * Cette méthode permet de charger les commentaires associés à une figure,
     * triés du plus récent au plus ancien, avec une pagination (10 par page).
     *
     * @param string            $slug              Le slug de la figure
     * @param Request           $request           La requête HTTP contenant les paramètres de pagination
     * @param FigureRepository  $figureRepository  Le repository pour accéder aux figures
     * @param CommentRepository $commentRepository Le repository pour accéder aux commentaires
     *
     * @return Response La réponse HTTP contenant le rendu des commentaires
     */
    #[Route('/figure/{slug}/comments', name: 'app_figure_comments', methods: ['GET'])]
    public function comments(
        string $slug,
        Request $request,
        FigureRepository $figureRepository,
        CommentRepository $commentRepository,
    ): Response {
        $figure = $figureRepository->findOneBy(['slug' => $slug]);

        if (!$figure) {
            throw $this->createNotFoundException('Figure introuvable.');
        }

        $page = $request->query->getInt('page', 1);
        $comments = $commentRepository->findByFigureWithPagination($figure->getId(), $page, 10);

        return $this->render(
            'figure/comments.html.twig',
            [
                'figure'   => $figure,
                'comments' => $comments,
                'page'     => $page,
            ]
        );
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
     * Trouve une figure par son ID ou lance une exception.
     *
     * @param int              $id               L'identifiant de la figure
     * @param FigureRepository $figureRepository Le repository pour accéder aux figures
     *
     * @return Figure La figure trouvée
     */
    private function findFigureById(int $id, FigureRepository $figureRepository): Figure
    {
        $figure = $figureRepository->find($id);

        if (!$figure) {
            throw $this->createNotFoundException('La figure demandée n\'existe pas.');
        }

        return $figure;
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
     * @param bool                   $remove         Indique si l'entité doit être supprimée (true) ou sauvegardée (false)
     *
     * @return void
     */
    private function saveEntity(EntityManagerInterface $entityManager, object $entity, string $successMessage, bool $remove = false): bool
    {
        try {
            if ($remove) {
                $entityManager->remove($entity);
            } else {
                $entityManager->persist($entity);
            }

            $entityManager->flush();
            $this->addFlash('success', $successMessage);

            return true;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la sauvegarde : '.$e->getMessage());

            return false;
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
