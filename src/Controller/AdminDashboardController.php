<?php

namespace App\Controller;


use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
  /**
   * @Route("/admin", name="admin_dashboard")
   * @param UserRepository $userRepository
   * @param RecetteRepository $recetteRepository
   * @return Response
   */
    public function index(UserRepository $userRepository, RecetteRepository $recetteRepository)
    {
      $entityManager = $this->getDoctrine ()->getManager ();
      $totalUsers = $entityManager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
      $totalRecettes = $entityManager->createQuery('SELECT COUNT(r) FROM App\Entity\Recette r')->getSingleScalarResult();
      $totalComments = $entityManager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
      $newUsers = $userRepository->findUserByDate (5);
      $newRecettes = $recetteRepository->findByDate (5);



        return $this->render('admin/admin_dashboard/index.html.twig', [
          'stats' => compact ('totalUsers', 'totalRecettes', 'totalComments'),
          'newusers' => $newUsers,
          'newrecettes' => $newRecettes
        ]);
    }
}
