<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Form\ImageType;
use App\Form\MainImageType;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figure/image')]
class FigureImageController extends AbstractController
{


    /**
     * Constructeur du contrôleur FigureImageController.
     *
     * @param FileUploader $fileUploader Service pour l'upload/suppression de fichiers
     * @param ImageResizer $imageResizer service pour le redimensionnement des images uplodées
     *
     * @return void
     */
    public function __construct(
        private FileUploader $fileUploader,
        private ImageResizer $imageResizer,
    ) {
    }


    /**
     * Crée ou édite une image pour une figure.
     *
     * Routes :
     *  - "/figure/image/add/{figureId}"  => app_figure_add_image (création)
     *  - "/figure/image/edit/{id}"       => app_figure_edit_image (édition)
     *
     * @param Request                $request  La requête HTTP
     * @param EntityManagerInterface $em       Le gestionnaire d'entités Doctrine
     * @param int|null               $figureId L'ID de la figure (pour la création, null pour édition)
     * @param Image|null             $image    L'entité Image à modifier (null si création)
     *
     * @return Response|RedirectResponse
     */
    #[Route('/add/{figureId}', name: 'app_figure_add_image', methods: ['GET', 'POST'])]
    #[Route('/edit/{id}', name: 'app_figure_edit_image', methods: ['GET', 'POST'])]
    public function formImage(Request $request, EntityManagerInterface $em, ?int $figureId = null, ?Image $image = null): Response|RedirectResponse
    {
        $isEdit = ($image && $image->getId());

        if (!$isEdit) {
            $image = new Image();

            if ($figureId) {
                $figure = $em->getRepository(Figure::class)->find($figureId);
                if (!$figure) {
                    $this->addFlash('error', 'Figure introuvable pour associer une image.');

                    return $this->redirectToRoute('app_home');
                }

                $image->setFigure($figure);
            }
        } else {
            $this->denyAccessUnlessGranted('IMAGE_EDIT', $image);
        }

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();
            if ($uploadedFile) {
                $newFilename = $this->fileUploader->upload($uploadedFile);
                if ($newFilename) {
                    $fullPath = $this->fileUploader->getTargetDirectory().'/'.$newFilename;

                    // Redimensionnement
                    $this->imageResizer->resize($fullPath, $fullPath, 600, 400);

                    $image->setUrl('/uploads/'.$newFilename);
                }
            }

            $em->persist($image);
            $em->flush();

            $this->addFlash('success', $isEdit ? "L'image a été modifée avec succès." : "L'image a été ajoutée avec succès.");

            $figure = $image->getFigure();
            if ($figure) {
                return $this->redirectToRoute(
                    'app_figure_detail',
                    [
                        'id'   => $figure->getId(),
                        'slug' => $figure->getSlug(),
                    ]
                );
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'figure_image/form.html.twig',
            [
                'form'   => $form->createView(),
                'image'  => $image,
                'isEdit' => $isEdit,
            ]
        );
    }


    /**
     * Supprime une image existante.
     *
     * @param Image                  $image   L'image à supprimer
     * @param Request                $request La requête HTTP
     * @param EntityManagerInterface $em      Le gestionnaire d'entités Doctrine
     *
     * @return RedirectResponse
     */
    #[Route('/delete/{id}', name: 'app_figure_delete_image', methods: ['POST'])]
    public function deleteImage(Image $image, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IMAGE_DELETE', $image);

        if (!$this->isCsrfTokenValid('delete_image_'.$image->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_home');
        }

        if (!$this->fileUploader->remove($image->getUrl())) {
            $this->addFlash('error', 'Erreur lors de la suppression du fichier image.');

            $figure = $image->getFigure();
            if ($figure) {
                return $this->redirectToRoute(
                    'app_figure_edit',
                    [
                        'id'   => $figure->getId(),
                    ]
                );
            }

            return $this->redirectToRoute('app_home');
        }

        $figure = $image->getFigure();
        $em->remove($image);
        $em->flush();

        $this->addFlash('success', 'Image supprimée avec succès.');

        if ($figure) {
            return $this->redirectToRoute(
                'app_figure_edit',
                [
                    'id'   => $figure->getId(),
                ]
            );
        }

        return $this->redirectToRoute('app_home');

    }


    /**
     * Définit une image en tant qu'image principale pour une figure.
     *
     * @param int                    $id      L'ID de la figure
     * @param Request                $request La requête HTTP
     * @param EntityManagerInterface $em      Le gestionnaire d'entités Doctrine
     *
     * @return RedirectResponse
     */
    #[Route('/figure/{id}/set-main-image', name: 'app_figure_set_main_image', methods: ['POST'])]
    public function setMainImage(int $id, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $em->getRepository(Figure::class)->find($id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        // Formulaire pour désigner l'image principale
        $form = $this->createForm(MainImageType::class, null, ['figure' => $figure]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedImageId = $form->get('mainImage')->getData();
            $selectedImage = $em->getRepository(Image::class)->find($selectedImageId);

            if ($selectedImage) {
                $figure->setMainImage($selectedImage);
                $em->flush();
                $this->addFlash('success', 'Image principale mise à jour avec succès.');
            } else {
                $this->addFlash('error', "L'image sélectionnée est introuvable.");
            }
        } else {
            $this->addFlash('error', "Le Formulaire contient des erreurs ou n'a pas été soumis correctement.");
        }

        // On redirige vers la page précédente (ou figure_detail)
        $referer = $request->headers->get('referer');
        if (!$referer) {
            return $this->redirectToRoute(
                'app_figure_detail',
                [
                    'id'   => $figure->getId(),
                    'slug' => $figure->getSlug(),
                ]
            );
        }

        return $this->redirect($referer);
    }


    /**
     * Supprime l'image principale d'une figure.
     *
     * @param int                    $id      L'ID de la figure
     * @param Request                $request La requête HTTP contenant le token CSRF
     * @param EntityManagerInterface $em      Le gestionnaire d'entités Doctrine
     *
     * @return RedirectResponse
     */
    #[Route('/figure/{id}/remove-main-image', name: 'app_figure_remove_main_image', methods: ['POST'])]
    public function removeMainImage(int $id, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $em->getRepository(Figure::class)->find($id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        if (!$this->isCsrfTokenValid('remove_main_image_'.$figure->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_figure_detail', ['id' => $figure->getId(), 'slug' => $figure->getSlug()]);
        }

        $figure->setMainImage(null);
        $em->flush();

        $this->addFlash('success', "L'image principale a été supprimée avec succès.");

        // Redirection vers le referer ou la page de détail si aucun referer
        $referer = $request->headers->get('referer');
        if (!$referer) {
            return $this->redirectToRoute(
                'app_figure_detail',
                [
                    'id'   => $figure->getId(),
                    'slug' => $figure->getSlug(),
                ]
            );
        }

        return $this->redirect($referer);
    }


}
