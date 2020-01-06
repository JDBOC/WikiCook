<?php

namespace App\Controller;

use App\Entity\Images;
use App\Form\ImagesType;
use App\Repository\ImagesRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImagesController extends AbstractController
{
  /**
   * @Route("/images", name="images")
   * @param ImagesRepository $imagesRepository
   * @return Response
   */
    public function index(ImagesRepository $imagesRepository): Response
    {
        return $this->render('images/index.html.twig', [
            'images' => $imagesRepository->findAll (),
        ]);
    }

  /**
   * @Route("/images/new", name="images_new")
   * @param Request $request
   * @return RedirectResponse|Response
   * @throws \Exception
   */
    public function new(Request $request)
    {
      $image = new Images();
      $form = $this->createForm (ImagesType::class, $image);
      $form->handleRequest ($request);

      if ($form->isSubmitted () && $form->isValid ()) {
        $entityManager = $this->getDoctrine ()->getManager ();
        $image->setImageSize (503);

        $entityManager->persist ($image);
        $entityManager->flush ();

        $this->addFlash ('success', "Image correctement enregistrée");

        return $this->redirectToRoute ('images');
      }
      return$this->render ('images/new.html.twig', [
        'images' => $image,
          'form' => $form->createView ()
        ]

      );
    }
}
