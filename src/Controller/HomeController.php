<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{


    /**
     * Affiche la liste des figures de snowboard.
     *
     * @param FigureRepository $figureRepository Le dépôt des figures
     *
     * @return Response La réponse HTTP
     */
    #[Route('/', name: 'app_home')]
    public function index(FigureRepository $figureRepository): Response
    {

        $figures = $figureRepository->findAllWithImages();

        return $this->render(
            'home/index.html.twig',
            [
                'figures' => $figures,
            ]
        );
    }


}
