<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Entity\Ingredient;
use App\Repository\EtapeRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recette")
 */
class RecetteController extends AbstractController
{
  /**
   * @Route("/", name="recette_index", methods={"GET"})
   * @param RecetteRepository $recetteRepository
   * @return Response
   */
    public function index(RecetteRepository $recetteRepository): Response
    {
        return $this->render('recette/index.html.twig', [
            'recettes' => $recetteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="recette_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
        $recette = new Recette();
        $user = $this->getUser ();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $recette->initializeSlug ();
          $recette->setAuthor ($user);
            $entityManager = $this->getDoctrine()->getManager();

            foreach ($recette->getIngredient () as $ingredients) {
              $ingredients -> setRecette ($recette);
              $entityManager->persist ($ingredients);
            }

            foreach ($recette->getEtape () as $etape) {
              $etape -> setRecette ($recette);
              $entityManager -> persist ($etape);
            }


            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('recette_index');
        }

        return $this->render('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form->createView (),
        ]);
    }

  /**
   * @Route("/{slug}", name="recette_show", methods={"GET"})
   * @param Recette $recette
   * @param IngredientRepository $ingredientRepository
   * @param EtapeRepository $etapeRepository
   * @return Response
   */
    public function show(Recette $recette, IngredientRepository $ingredientRepository, EtapeRepository $etapeRepository): Response
    {

        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
            'ingredients' => $ingredientRepository->findByRecette ($recette),
            'etapes' => $etapeRepository->findByRecette ($recette)
        ]);
    }

  /**
   * @Route("/{slug}/edit", name="recette_edit", methods={"GET","POST"})
   * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="vous ne pouvez pas modifier les annonces des autres utilisateurs")
   * @param Request $request
   * @param Recette $recette
   * @return Response
   */
    public function edit(Request $request, Recette $recette): Response
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

          foreach ($recette->getIngredient () as $ingredients) {
            $ingredients -> setRecette ($recette);
            $entityManager->persist ($ingredients);
          }

          foreach ($recette->getEtape () as $etape) {
            $etape -> setRecette ($recette);
            $entityManager -> persist ($etape);
          }

          $entityManager->flush ();

            return $this->redirectToRoute('recette_show', [
              'slug' => $recette->getSlug ()
            ]);
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
        ]);
    }


  /**
   * @Route("/{slug}", name="recette_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="vous ne pouvez pas supprimer cette annonce")
   * @param Request $request
   * @param Recette $recette
   * @return Response
   */
    public function delete(Request $request, Recette $recette): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recette_index');
    }
}
