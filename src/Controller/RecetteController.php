<?php

  namespace App\Controller;

  use App\Entity\Categorie;
  use App\Entity\Comment;
  use App\Entity\Recette;
  use App\Form\CommentType;
  use App\Form\RecetteType;
  use App\Repository\CategorieRepository;
  use App\Repository\CommentRepository;
  use App\Repository\EtapeRepository;
  use App\Repository\IngredientRepository;
  use App\Repository\RecetteRepository;
  use App\Service\Pagination;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use Symfony\Component\HttpFoundation\File\Exception\FileException;
  use Symfony\Component\HttpFoundation\File\UploadedFile;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  /**
   * @Route("/recette")
   */
  class RecetteController extends AbstractController
  {
    /**
     * @Route("/{page<\d+>?1}", name="recette_index", methods={"GET"})
     * @param RecetteRepository $recetteRepository
     * @param $page
     * @param Pagination $pagination
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function index(RecetteRepository $recetteRepository , $page , Pagination $pagination , CategorieRepository $categorieRepository): Response
    {
      $pagination->setEntityClass ( Recette::class )
        ->setPage ( $page )
        ->setLimit ( 12 );

      $recipes = $pagination->getData ();


      $total = count ( $recetteRepository->findAll () );
      $pages = ceil ( $total / 8 );

      return $this->render ( 'recette/index.html.twig' , [
        'recettes' => $recipes ,
        'categories' => $categorieRepository->findAll () ,
        'pages' => $pages ,
        'page' => $page
      ] );
    }

    /**
     * @Route("/new", name="recette_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
      $recette = new Recette();
      $user = $this->getUser ();
      $form = $this->createForm ( RecetteType::class , $recette );
      $form->handleRequest ( $request );

      if ($form->isSubmitted () && $form->isValid ()) {
        $recette->initializeSlug ();
        $recette->setAuthor ( $user );

        /** @var UploadedFile $brochureFile */
        $brochureFile = $form['image']->getData ();
        if ($brochureFile) {
          $originalFilename = pathinfo ( $brochureFile->getClientOriginalName () , PATHINFO_FILENAME );
          $newFilename = $originalFilename . '-' . uniqid ( '' , true ) . '.' . $brochureFile->guessExtension ();
          try {
            $brochureFile->move (
              $this->getParameter ( 'images_directory' ) ,
              $newFilename
            );
          } catch (FileException $e) {

          }
          $recette->setImage ( $newFilename );
        }

        $entityManager = $this->getDoctrine ()->getManager ();

        foreach ($recette->getIngredient () as $ingredients) {
          $ingredients->setRecette ( $recette );
          $entityManager->persist ( $ingredients );
        }

        foreach ($recette->getEtape () as $etape) {
          $etape->setRecette ( $recette );
          $entityManager->persist ( $etape );
        }
        $entityManager->persist ( $recette );
        $entityManager->flush ();

        return $this->redirectToRoute ( 'recette_index' );
      }

      return $this->render ( 'recette/new.html.twig' , [
        'recette' => $recette ,
        'form' => $form->createView () ,
      ] );
    }


    /**
     * @Route("/{slug}", name="recette_show")
     * @param Recette $recette
     * @param IngredientRepository $ingredientRepository
     * @param EtapeRepository $etapeRepository
     * @param CommentRepository $commentRepository
     * @param CategorieRepository $categorieRepository
     * @param Request $request
     * @return Response
     */
    public function show(Recette $recette ,
                         IngredientRepository $ingredientRepository ,
                         EtapeRepository $etapeRepository ,
                         CommentRepository $commentRepository ,
                         CategorieRepository $categorieRepository ,
                         Request $request): Response
    {
      $comment = new Comment();
      $user = $this->getUser ();
      $form = $this->createForm ( CommentType::class , $comment );
      $form->handleRequest ( $request );

      if ($form->isSubmitted () && $form->isValid ()) {
        $comment->setAuthor ( $user );
        $comment->setRecette ( $recette );
        $entityManager = $this->getDoctrine ()->getManager ();
        $entityManager->persist ( $comment );
        $entityManager->flush ();

        $this->addFlash ( 'success' , "Votre commentaire a été enregistré" );
      }
      return $this->render ( 'recette/show.html.twig' , [
        'recette' => $recette ,
        'form' => $form->createView () ,
        'ingredients' => $ingredientRepository->findByRecette ( $recette ) ,
        'etapes' => $etapeRepository->findByRecette ( $recette ) ,
        'comments' => $commentRepository->findByRecette ( $recette ) ,
        'categories' => $categorieRepository->findAll ()
      ] );
    }


    /**
     * @Route("/{slug}/edit", name="recette_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER') and user == recette.getAuthor()", message="vous ne pouvez pas modifier les annonces dont vous n'êtes pas l'auteur")
     * @param Request $request
     * @param Recette $recette
     * @return Response
     */
    public function edit(Request $request , Recette $recette): Response
    {
      $form = $this->createForm ( RecetteType::class , $recette );
      $form->handleRequest ( $request );

      if ($form->isSubmitted () && $form->isValid ()) {


        /** @var UploadedFile $brochureFile */
        $brochureFile = $form['image']->getData ();
        if ($brochureFile) {
          $originalFilename = pathinfo ( $brochureFile->getClientOriginalName () , PATHINFO_FILENAME );
          $newFilename = $originalFilename . '-' . uniqid ( '' , true ) . '.' . $brochureFile->guessExtension ();
          try {
            $brochureFile->move (
              $this->getParameter ( 'images_directory' ) ,
              $newFilename
            );
          } catch (FileException $e) {

          }
          $recette->setImage ( $newFilename );
        }


        $entityManager = $this->getDoctrine ()->getManager ();

        foreach ($recette->getIngredient () as $ingredients) {
          $ingredients->setRecette ( $recette );
          $entityManager->persist ( $ingredients );
        }

        foreach ($recette->getEtape () as $etape) {
          $etape->setRecette ( $recette );
          $entityManager->persist ( $etape );
        }

        $entityManager->flush ();

        return $this->redirectToRoute ( 'recette_show' , [
          'slug' => $recette->getSlug ()
        ] );
      }

      return $this->render ( 'recette/edit.html.twig' , [
        'recette' => $recette ,
        'form' => $form->createView () ,
      ] );
    }


    /**
     * @Route("/{id}/delete", name="recette_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_USER') and user == recette.getAuthor()", message="vous ne pouvez pas modifier les annonces dont vous n'êtes pas l'auteur")
     * @param Recette $recette
     * @return Response
     */
    public function delete(Recette $recette): Response
    {


      $entityManager = $this->getDoctrine ()->getManager ();

      $entityManager->remove ( $recette );
      $entityManager->flush ();

      $this->addFlash (
        'success' ,
        "La recette <strong>{$recette->getTitle ()}</strong> a bien été supprimée "
      );
      return $this->redirectToRoute ( 'recette_index' );


    }
  }



