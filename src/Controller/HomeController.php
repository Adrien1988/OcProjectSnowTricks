<?php

namespace App\Controller;

use App\Form\FigureType;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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


    /**
     * Charge dynamiquement plus de figures pour le scroll infini ou un bouton "Voir plus".
     *
     * Cette méthode est appelée via une requête AJAX pour charger une portion supplémentaire
     * de figures paginées avec leurs images, en fonction d'un offset passé en query string.
     *
     * @param Request          $request          La requête HTTP contenant l'offset
     * @param FigureRepository $figureRepository Le repository pour accéder aux figures
     *
     * @return Response La réponse contenant le rendu HTML partiel à injecter
     */
    #[Route('/load-more-figures', name: 'app_load_more_figures', methods: ['GET'])]
    public function loadMoreFigures(Request $request, FigureRepository $figureRepository): JsonResponse
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = 15;

        // Étape 1 : récupérer les IDs
        $idResults = $figureRepository->findPaginatedFigureIds($limit, $offset);
        $ids = array_column($idResults, 'id');

        // Étape 2 : charger les figures
        $figures = $figureRepository->findFiguresWithImages($ids);

        // Étape 3 : savoir s'il en reste d'autres
        $nextIdResults = $figureRepository->findPaginatedFigureIds(1, $offset + $limit);
        $hasMore = !empty($nextIdResults);

        $html = $this->renderView('partials/_figures_partial.html.twig', [
            'figures' => $figures,
        ]);

        // Création de la réponse JSON avec les bons headers cache
        $response = new JsonResponse([
            'html'    => $html,
            'hasMore' => $hasMore,
        ]);

        $response->setPublic();
        $response->setMaxAge(60); // 60s
        $response->setImmutable(); // Ne pas revalider pendant ce temps

        return $response;
    }


}
