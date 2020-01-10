<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RecetteRepository $recetteRepository, CategorieRepository $categorieRepository)
    {
        return $this->render('home/index.html.twig', [
          'categories' => $categorieRepository->findAll (),
          'recettes' => $recetteRepository->findByDate (3)
        ]);
    }
}
