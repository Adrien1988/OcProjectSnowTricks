<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class FigureController extends AbstractController
{

    /**
     * Crée une nouvelle figure.
     *
     * Vérifie que l'utilisateur est authentifié et enregistre la figure en base.
     *
     * @param Request                $request          requête HTTP
     * @param EntityManagerInterface $entityManager    gestionnaire d'entités
     * @param FigureRepository       $figureRepository repository des figures
     *
     * @return Response
     */
    #[Route('/figure/new', name: 'app_figure_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, FigureRepository $figureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($figureRepository->findOneBy(['name' => $figure->getName()])) {
                $this->addFlash('danger', 'Une figure avec ce nom existe déjà.');

                return $this->render(
                    'figure/new.html.twig',
                    [
                        'form' => $form->createView(),
                    ]
                );
            }

            // Persiste et enregistre la figure
            $entityManager->persist($figure);
            $entityManager->flush();

            $this->addFlash('success', 'La figure a été créée avec succès.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'figure/new.html.twig',
            [
                'form' => $form->createView(),
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
