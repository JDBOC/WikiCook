<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
  /**
   * @Route("/admin/login", name="admin_account_login")
   * @param AuthenticationUtils $utils
   * @return Response
   */
    public function adminLogin(AuthenticationUtils $utils)
    {
      $error = $utils->getLastAuthenticationError ();
      $username = $utils->getLastUsername ();
        return $this->render('admin/accountAdmin/login.html.twig', [
          'hasError'  => $error !== null,
          'username'  => $username
        ]);
    }

  /**
   * @Route("/admin/logout", name="admin_account_logout")
   * @return void
   */
  public function logout(){

  }
}
