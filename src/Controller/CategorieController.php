<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\RecetteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie")
 */
class CategorieController extends AbstractController
{
    /**
     * @Route("/", name="categorie_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="categorie_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

  /**
   * @Route("/categorie/{id}", name="categorie_show")
   *
   * @param Categorie $categorie
   * @param CategorieRepository $categorieRepository
   * @param RecetteRepository $recetteRepository
   * @return Response
   */
  public function showCat(Categorie $categorie, CategorieRepository $categorieRepository, RecetteRepository $recetteRepository): Response
  {

    return $this->render('categorie/show.html.twig', [
      'categories' => $categorieRepository->findAll (),
      'recettes' => $recetteRepository->findByCategorie ($categorie),
      'slug' => $categorie->getSlug (),
      'categorie' => $categorie
    ]);
  }

    /**
     * @Route("/{id}/edit", name="categorie_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_index');
    }
}
