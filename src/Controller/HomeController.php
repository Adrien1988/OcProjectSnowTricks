<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{


    /**
     * Affiche la liste des figures de snowboard et permet d'en créer une via une modale.
     *
     * @param FigureRepository       $figureRepository Le dépôt des figures
     * @param Request                $request          La requête HTTP
     * @param EntityManagerInterface $entityManager    Gestionnaire d'entités pour persister les données
     * @param SluggerInterface       $slugger          Interface permettant de transformer une chaîne en slug unique
     *
     * @return Response La réponse HTTP
     */
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(FigureRepository $figureRepository, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $figures = $figureRepository->findAllWithImages();

        // Création du formulaire pour ajouter une figure
        $figure = new Figure();
        $createFigureForm = $this->createForm(FigureType::class, $figure);
        $createFigureForm->handleRequest($request);

        // Gestion de la soumission du formulaire
        if ($createFigureForm->isSubmitted() && $createFigureForm->isValid()) {
            // Générer le slug avant la persistance
            $figure->generateSlug($slugger);

            $figure->setAuthor($this->getUser());

            $entityManager->persist($figure);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'La figure a été créée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création de la figure.');
            }

            return $this->redirectToRoute('app_home');
        }

        // Si le formulaire est soumis mais non valide, afficher les erreurs
        if ($createFigureForm->isSubmitted() && !$createFigureForm->isValid()) {
            $errors = [];
            foreach ($createFigureForm->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire de création de figure : '.implode(' - ', $errors));
            }
        }

        return $this->render(
            'home/index.html.twig',
            [
                'figures'          => $figures,
                'createFigureForm' => $createFigureForm->createView(),
            ]
        );
    }


}
