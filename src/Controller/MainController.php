<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'accueil_main')]
    public function index(
        SortieRepository $sortieRepository
    ): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('main/index.html.twig', [
            "sorties" => $sorties,
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/erro???r??????', name: 'black')]
    public function black(
        SortieRepository $sortieRepository
    ): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('main/black.html.twig', [
            "sorties" => $sorties,
            'controller_name' => 'MainController',
        ]);
    }

}
