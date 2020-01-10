<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Repository\CategorieRepository;
use App\Repository\RecetteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AccountController extends AbstractController
{
  /**
   * @Route("/login", name="account_login")
   * @param AuthenticationUtils $utils
   * @return Response
   */
    public function login(AuthenticationUtils $utils, CategorieRepository $repository)
    {
      $error = $utils->getLastAuthenticationError ();
      $username = $utils->getLastUsername ();

        return $this->render('account/login.html.twig', [
            'hasError'  => $error !== null,
            'username'  => $username,
          'categories' => $repository->findAll ()
        ]);
    }

  /**
   * @Route("/logout", name="account_logout")
   * @return Response
   */
    public function logout(){

    }

  /**
   * Formulaire d'inscription
   *
   * @Route("/register", name="account_register")
   * @param Request $request
   * @param UserPasswordEncoderInterface $encoder
   * @return Response
   */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, CategorieRepository $repository){
      $user = new User();

      $form = $this->createForm (RegistrationType::class, $user);
      $form ->handleRequest ($request);

      if ($form -> isSubmitted () && $form->isValid ()) {
        /** @var UploadedFile $brochureFile */
        $brochureFile = $form['picture']->getData();
        if ($brochureFile) {
          $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
          $newFilename = $originalFilename.'-'.uniqid( '' , true ).'.'.$brochureFile->guessExtension();
          try {
            $brochureFile->move(
              $this->getParameter('user_directory'),
              $newFilename
            );
          } catch (FileException $e) {

          }
          $user->setPicture($newFilename);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $hash = $encoder->encodePassword ($user, $user->getHash ());
        $user->setHash ($hash);
        $entityManager->persist ($user);
        $entityManager->flush ();

        return $this->redirectToRoute ('home');
      }

      return $this->render ('account/registration.html.twig', [
        'form' => $form->createView (),
        'categories' => $repository->findAll ()
      ]);
    }

  /**
   * @Route("/account/profil", name="account_profil")
   * @IsGranted("ROLE_USER")
   * @param Request $request
   * @return Response
   */
    public function profil(Request $request, CategorieRepository $repository){
      $user = $this->getUser ();
      $form = $this->createForm (AccountType::class, $user);
      $form->handleRequest ($request);

      if ($form->isSubmitted ()&& $form->isValid ()) {
        /** @var UploadedFile $brochureFile */
        $brochureFile = $form['picture']->getData();
        if ($brochureFile) {
          $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
          $newFilename = $originalFilename.'-'.uniqid( '' , true ).'.'.$brochureFile->guessExtension();
          try {
            $brochureFile->move(
              $this->getParameter('user_directory'),
              $newFilename
            );
          } catch (FileException $e) {

          }
          $user->setPicture($newFilename);
        }
        $entityManager = $this->getDoctrine ()->getManager ();
        $entityManager->persist ($user);
        $entityManager -> flush ();

        $this->addFlash (
          'success', "Modification effectuée"
        );
      }

      return $this->render ('account/profil.html.twig', [
        'form' => $form->createView (),
        'categories' => $repository->findAll ()
      ]);
    }

  /**
   * @Route("/account/password-update", name="account_password")
   * @IsGranted("ROLE_USER")
   * @param Request $request
   * @param UserPasswordEncoderInterface $encoder
   * @return Response
   */
    public function modifPassword(Request $request, UserPasswordEncoderInterface $encoder){

      $user = $this->getUser ();
      $passwordUpdate = new PasswordUpdate();

      $form = $this->createForm (PasswordUpdateType::class, $passwordUpdate);
      $form->handleRequest ($request);

      if ($form->isSubmitted () && $form->isValid ()) {
        if (!password_verify ($passwordUpdate->getOldPassword (), $user->getHash())){

          $form->get ('oldPassword')->addError (new FormError("Mauvais mot de passe actuel"));

        }
        else {
          $newPassword = $passwordUpdate->getNewPassword ();
          $hash = $encoder->encodePassword($user, $newPassword);
          $user->setHash($hash);
          $entityManager = $this->getDoctrine ()->getManager ();
          $entityManager->persist($user);
          $entityManager->flush();

          $this->addFlash ('success', "Votre mot de passe a été modifié");

          return $this->redirectToRoute ('recette_index');
        }
      }

    return $this->render ('account/password.html.twig', [
      'form' => $form->createView ()
    ]);
    }

  /**
   * profil utilisateur
   *
   * @Route("/account", name="account_index")
   * @IsGranted("ROLE_USER")
   *
   * @return Response
   */
    public function myAccount(RecetteRepository $repository, CategorieRepository $categorieRepository) {

      $user = $this->getUser ();
      $recettes = $repository->findByAuthor ($user);
      return $this->render ('user/index.html.twig', [
        'user' => $user,
        'recettes' => $recettes,
        'categories' => $categorieRepository->findAll ()
      ]);
    }
}
