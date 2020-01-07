<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Entity\User;
use App\Form\AccountType;
use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
  /**
   * @Route("/admin/user", name="admin_user_index")
   * @param UserRepository $userRepository
   * @return Response
   */
    public function index(UserRepository $userRepository)
    {
        return $this->render('admin/userAdmin/index.html.twig', [
            'users' => $userRepository->findAll (),
        ]);
    }

  /**
   * @Route("/admin/user/{id}/edit", name="admin_user_edit")
   */
    public function edit(User $user, Request $request)
    {
      $form = $this->createForm (AccountType::class, $user);
      $form->handleRequest ($request);

      if ($form->isSubmitted () && $form->isValid ()) {
        $entityManager = $this->getDoctrine ()->getManager ();
        $entityManager->persist ($user);
        $entityManager->flush ();

        $this->addFlash ('success', "Modification effectuée");

        return $this->redirectToRoute ('admin_user_index');
      }
      return $this->render ('admin/userAdmin/edit.html.twig', [
        'form' => $form->createView (),
        'user' => $user
      ]);
    }

  /**
   * @Route("/admin/user/{id}/delete", name="admin_user_delete")
   * @param User $user
   * @param RecetteRepository $recetteRepository
   * @return RedirectResponse
   */
  public function delete(User $user) {



    $entityManager = $this->getDoctrine ()->getManager ();
    $author = $user->getId ();
    $repoRecettes = $this->getDoctrine ()->getRepository (Recette::class);
    $recetteAuthor = $repoRecettes->findBy (['author' => $author]);
    foreach ($recetteAuthor as $value) {
      $value->setAuthor (null);
      $entityManager->persist ($value);
      $entityManager->flush ();
    }


    $entityManager->remove ($user);


    $entityManager->flush ();

    $this->addFlash ('success', "suppression effectuée");

    return $this->redirectToRoute ('admin_user_index');

  }
}
