<?php

  namespace App\Controller;


  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use Symfony\Component\HttpFoundation\Request;

  class SearchController extends AbstractController
  {
    /**
     * @Route("/recette/search", name="search_recette")
     */
    public function searchRecette(Request $request)
    {

      return $this->render ('partiels/_search.html.twig');
    }
  }