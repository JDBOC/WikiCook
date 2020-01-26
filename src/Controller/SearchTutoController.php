<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchForm;
use App\Repository\CategorieRepository;
use App\Repository\RecetteRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchTutoController extends AbstractController
{
    /**
     * @Route("/search/tuto", name="search_tuto")
     */
    public function index(RecetteRepository $recetteRepository, CategorieRepository $categorieRepository, Request $request)
    {
      $data = new SearchData();
      $form = $this->createForm (SearchForm::class, $data);
      $form->handleRequest ($request);
      $results = $recetteRepository->findSearch ($data);
        return $this->render('search_tuto/index.html.twig', [
          'categories' => $categorieRepository->findAll () ,
            'recettes' => $results,
            'form' => $form->createView ()
        ]);
    }
}
