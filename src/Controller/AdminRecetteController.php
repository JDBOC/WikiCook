<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecetteController extends AbstractController
{
    /**
     * @Route("/admin/recette", name="admin_recette_index")
     */
    public function index(RecetteRepository $repository)
    {
        return $this->render('admin/recetteAdmin/index.html.twig', [
            'recettes' => $repository->findAll ()
        ]);
    }

  /**
   * @Route("/admin/recette/{id}/edit", name="admin_recette_edit")
   * @param Recette $recette
   * @return Response
   */
    public function edit(Recette $recette, Request $request){
      $form = $this->createForm (RecetteType::class, $recette);
      $form->handleRequest ($request);

      if ($form->isSubmitted () && $form->isValid ()) {
        $entityManager = $this->getDoctrine ()->getManager ();
        $entityManager->persist ($recette);
        $entityManager->flush ();

        $this->addFlash ('success', "Modification effectuée");
      }

      return $this->render ('admin/recetteAdmin/edit.html.twig', [
        'recette' => $recette,
        'form' => $form->createView ()
      ]);
    }

  /**
   * @Route("/admin/recette/{id}/delete", name="admin_recette_delete")
   * @param Recette $recette
   * @return Response
   */
    public function delete(Recette $recette) {
      $entityManager = $this->getDoctrine ()->getManager ();
      $entityManager->remove ($recette);
      $entityManager->flush ();

      $this->addFlash ('success', "suppression effectuée");

      return $this->redirectToRoute ('admin_recette_index');

    }

}
