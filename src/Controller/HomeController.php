<?php

namespace App\Controller;

use App\Form\FigureType;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{


    /**
     * Affiche la liste des figures de snowboard et permet d'en créer une via une modale.
     *
     * @param FigureRepository $figureRepository Le dépôt des figures
     * @param Request          $request          La requête HTTP
     *
     * @return Response La réponse HTTP
     */
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(FigureRepository $figureRepository, Request $request): Response
    {

        $limit = 15;
        $offset = 0;

        // 1) Récupérer les IDs
        $idResults = $figureRepository->findPaginatedFigureIds($limit, $offset);
        // Convertir en un simple tableau d'IDs
        $ids = array_column($idResults, 'id');

        $figures = $figureRepository->findFiguresWithImages($ids);
        $createFigureForm = $this->createForm(FigureType::class);

        return $this->render(
            'home/index.html.twig',
            [
                'figures'          => $figures,
                'createFigureForm' => $createFigureForm->createView(),
                'openModal'        => false,
            ]
        );
    }


    #[Route('/load-more-figures', name: 'app_load_more_figures', methods: ['GET'])]
    public function loadMoreFigures(Request $request, FigureRepository $figureRepository): Response
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = 15;

        // 1) Récupérer uniquement les IDs des figures
        $idResults = $figureRepository->findPaginatedFigureIds($limit, $offset);
        $ids = array_column($idResults, 'id');

        // 2) Charger les figures + images
        $figures = $figureRepository->findFiguresWithImages($ids);

        // Rendre le partial
        return $this->render('partials/_figures_partial.html.twig', [
            'figures' => $figures,
        ]);
    }


}
