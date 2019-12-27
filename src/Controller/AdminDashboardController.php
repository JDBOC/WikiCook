<?php

namespace App\Controller;


use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index()
    {
      $entityManager = $this->getDoctrine ()->getManager ();
      $users = $entityManager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
      $recettes = $entityManager->createQuery('SELECT COUNT(r) FROM App\Entity\Recette r')->getSingleScalarResult();
      $comments = $entityManager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();


        return $this->render('admin/admin_dashboard/index.html.twig', [
          'stats' => compact ('users', 'recettes', 'comments')
        ]);
    }
}
