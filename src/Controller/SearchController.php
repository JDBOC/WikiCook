<?php

  namespace App\Controller;

  use App\Entity\Recette;
  use App\Form\RechercheType;
  use App\Repository\CategorieRepository;
  use App\Repository\RecetteRepository;
  use Doctrine\Persistence\ObjectManager;
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use Symfony\Component\Form\Extension\Core\Type\SearchType;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class SearchController extends AbstractController
  {
    /**
     * @Route("/search", name="search")
     * @param Request $request
     * @param CategorieRepository $categorieRepository
     * @param RecetteRepository $recetteRepository
     * @return Response
     */
    public function index(Request $request, CategorieRepository $categorieRepository, RecetteRepository $recetteRepository)
    {
      $form = $this->createForm (RechercheType::class);
      $form->handleRequest ($request);

      if ($form->isSubmitted () && $form->isValid ()){
        $recherche = $form->getData ();
        $results = $recetteRepository->findByRecherche ($recherche);

        return $this->render ('search/results.html.twig', [

          'categories' => $categorieRepository->findAll (),
          'recettes' => $results
        ]);
      }

      return $this->render ('search/index.html.twig', [
        'form' => $form->createView (),
        'categories' => $categorieRepository->findAll (),

      ]);
    }


  }