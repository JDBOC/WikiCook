<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
  /**
   * @Route("/search", name="search")
   * @param RecetteRepository $repository
   * @param CategorieRepository $categorieRepository
   * @return Response
   */
    public function index(RecetteRepository $repository, CategorieRepository $categorieRepository)
    {
      if (isset($_GET['#recherche'])) {
        $motclef = $_GET['#recherche'];
        $q = array('recherche' => $motclef. '%');

      }
        return $this->render('search/index.html.twig', [
            'categories' => $categorieRepository->findAll (),
            'resultats' => $resultats = $repository->findBySearch ()

        ]);
    }
}
