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
        if ($createFigureForm->isSubmitted()) {
            $this->addFlash('info', 'Le formulaire a été soumis.');
            if ($createFigureForm->isValid()) {
                $this->addFlash('success', 'Le formulaire est valide.');

                // Générer le slug avant la persistance
                $figure->generateSlug($slugger);

                $entityManager->persist($figure);
                $entityManager->flush();
        
                $this->addFlash('success', 'La figure a été créée avec succès.');
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs.');
                dump($createFigureForm->getErrors(true)); // Affiche les erreurs dans le log
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
