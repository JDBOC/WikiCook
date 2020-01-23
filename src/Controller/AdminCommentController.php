<?php

  namespace App\Controller;

  use App\Entity\Comment;
  use App\Repository\CommentRepository;
  use App\Service\Pagination;
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use Symfony\Component\HttpFoundation\RedirectResponse;
  use Symfony\Component\Routing\Annotation\Route;

  class AdminCommentController extends AbstractController
  {
    /**
     * @Route("/admin/comment/{page<\d+>?1}", name="admin_comment_index")
     */
    public function index(CommentRepository $commentRepository , Pagination $pagination , $page = 1)
    {
      $pagination->setEntityClass ( Comment::class )
        ->setPage ( $page )
        ->setLimit ( 10 );
      $comments = $pagination->getData ();
      $total = count ( $commentRepository->findAll () );
      $pages = ceil ( $total / 9 );

      $entityManager = $this->getDoctrine ()->getManager ();
      $totalUsers = $entityManager->createQuery ( 'SELECT COUNT(u) FROM App\Entity\User u' )->getSingleScalarResult ();
      $totalRecettes = $entityManager->createQuery ( 'SELECT COUNT(r) FROM App\Entity\Recette r' )->getSingleScalarResult ();
      $totalComments = $entityManager->createQuery ( 'SELECT COUNT(c) FROM App\Entity\Comment c' )->getSingleScalarResult ();
      return $this->render ( 'admin/commentAdmin/index.html.twig' , [
        'stats' => compact ( 'totalUsers' , 'totalRecettes' , 'totalComments' ) ,
        'comments' => $comments ,
        'pages' => $pages ,
        'page' => $page
      ] );
    }

    /**
     * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function delete(Comment $comment)
    {
      $entityManager = $this->getDoctrine ()->getManager ();
      $entityManager->remove ( $comment );
      $entityManager->flush ();

      $this->addFlash ( 'success' , 'Commentaire supprimé' );

      return $this->redirectToRoute ( 'admin_comment_index' );
    }
  }
