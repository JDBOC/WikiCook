<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;


use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
  /**
   * @Route("/login", name="account_login")
   * @param AuthenticationUtils $utils
   * @return Response
   */
    public function login(AuthenticationUtils $utils)
    {
      $error = $utils->getLastAuthenticationError ();
      $username = $utils->getLastUsername ();

        return $this->render('account/login.html.twig', [
            'hasError'  => $error !== null,
            'username'  => $username
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
    public function register(Request $request, UserPasswordEncoderInterface $encoder){
      $user = new User();

      $form = $this->createForm (RegistrationType::class, $user);
      $form ->handleRequest ($request);

      if ($form -> isSubmitted () && $form->isValid ()) {
        $entityManager = $this->getDoctrine()->getManager();
        $hash = $encoder->encodePassword ($user, $user->getHash ());
        $user->setHash ($hash);
        $entityManager->persist ($user);
        $entityManager->flush ();

        return $this->redirectToRoute ('home');
      }

      return $this->render ('account/registration.html.twig', [
        'form' => $form->createView ()
      ]);
    }

  /**
   * @Route("/account/profil", name="account_profil")
   * @param Request $request
   * @return Response
   */
    public function profil(Request $request){
      $user = $this->getUser ();
      $form = $this->createForm (AccountType::class, $user);
      $form->handleRequest ($request);

      if ($form->isSubmitted ()&& $form->isValid ()) {
        $entityManager = $this->getDoctrine ()->getManager ();
        $entityManager->persist ($user);
        $entityManager -> flush ();

        $this->addFlash (
          'success', "Modification effectuée"
        );
      }

      return $this->render ('account/profil.html.twig', [
        'form' => $form->createView ()
      ]);
    }

  /**
   * @Route("/account/password-update", name="account_password")
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
   *
   * @return Response
   */
    public function myAccount(RecetteRepository $repository) {

      $user = $this->getUser ();
      $recettes = $repository->findByAuthor ($user);
      return $this->render ('user/index.html.twig', [
        'user' => $user,
        'recettes' => $recettes
      ]);
    }
}
