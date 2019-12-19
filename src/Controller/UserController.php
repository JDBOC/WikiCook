<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RecetteRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}", name="user_show")
     */
    public function index(User $user, RecetteRepository $repository)
    {
      $recettes = $repository->findByAuthor($user);
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'recettes' => $recettes
        ]);
    }
}
