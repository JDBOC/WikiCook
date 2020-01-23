<?php

  namespace App\Controller;

  use App\Entity\Etape;
  use App\Entity\Ingredient;
  use App\Entity\Recette;
  use App\Form\RecetteType;
  use App\Repository\RecetteRepository;
  use App\Service\Pagination;
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class AdminRecetteController extends AbstractController
  {
    /**
     * @Route("/admin/recette/{page<\d+>?1}", name="admin_recette_index")
     * @param RecetteRepository $repository
     * @param Pagination $pagination
     * @param $page
     * @return Response
     */
    public function index(RecetteRepository $repository , Pagination $pagination , $page = 1)
    {
      $entityManager = $this->getDoctrine ()->getManager ();
      $totalUsers = $entityManager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
      $totalRecettes = $entityManager->createQuery('SELECT COUNT(r) FROM App\Entity\Recette r')->getSingleScalarResult();
      $totalComments = $entityManager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
      $pagination ->setEntityClass ( Recette::class )
                  ->setPage ( $page )
                  ->setLimit ( 10 );
      $recipes = $pagination->getData ();
      $total = count ( $repository->findAll () );
      $pages = ceil ( $total / 10 );
      return $this->render ( 'admin/recetteAdmin/index.html.twig' , [
        'stats' => compact ('totalUsers', 'totalRecettes', 'totalComments'),
        'recettes' => $recipes ,
        'pages' => $pages ,
        'page' => $page
      ] );
    }

    /**
     * @Route("/admin/recette/{id}/edit", name="admin_recette_edit")
     * @param Recette $recette
     * @return Response
     */
    public function edit(Recette $recette , Request $request)
    {
      $form = $this->createForm ( RecetteType::class , $recette );
      $form->handleRequest ( $request );

      if ($form->isSubmitted () && $form->isValid ()) {
        $entityManager = $this->getDoctrine ()->getManager ();
        $entityManager->persist ( $recette );
        $entityManager->flush ();

        $this->addFlash ( 'success' , "Modification effectuée" );
      }

      return $this->render ( 'admin/recetteAdmin/edit.html.twig' , [
        'recette' => $recette ,
        'form' => $form->createView ()
      ] );
    }

    /**
     * @Route("/admin/recette/{id}/delete", name="admin_recette_delete")
     * @param Recette $recette
     * @return Response
     */
    public function delete(Recette $recette)
    {
      $entityManager = $this->getDoctrine ()->getManager ();

      $entityManager->remove ( $recette );
      $entityManager->flush ();

      $this->addFlash ( 'success' , "suppression effectuée" );

      return $this->redirectToRoute ( 'admin_recette_index' );

    }

  }
