<?php

namespace App\Controller;

use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
