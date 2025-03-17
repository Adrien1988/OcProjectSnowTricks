<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{


    #[Route('/figure', name: 'figure_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $figures = $em->getRepository(Figure::class)->findAll();

        return $this->render('figure/index.html.twig', compact('figures'));
    }


    #[Route('/figure/new', name: 'figure_create')]
    #[Route('/figure/{id}/edit', name: 'figure_edit')]
    public function form(?Figure $figure = null, Request $request, EntityManagerInterface $em): Response
    {
        if (!$figure) {
            $figure = new Figure();
        }

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($figure);
            $em->flush();

            return $this->redirectToRoute('figure_index');
        }

        return $this->render(
            'figure/form.html.twig',
            [
                'form'     => $form->createView(),
                'editMode' => $figure->getId() !== null,
            ]
        );
    }


    #[Route('/figure/{id}/delete', name: 'figure_delete', methods: ['POST'])]
    public function delete(Figure $figure, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$figure->getId(), $request->request->get('_token'))) {
            $em->remove($figure);
            $em->flush();
        }

        return $this->redirectToRoute('figure_index');
    }


}
