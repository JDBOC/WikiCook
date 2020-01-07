<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comment", name="admin_comment_index")
     */
    public function index(CommentRepository $commentRepository)
    {
      $comments = $commentRepository->findAll ();
        return $this->render('admin/commentAdmin/index.html.twig', [
            'comments' => $comments
        ]);
    }

  /**
   * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
   * @param Comment $comment
   * @return RedirectResponse
   */
    public function delete(Comment $comment)
    {
      $entityManager = $this->getDoctrine ()->getManager ();
      $entityManager->remove ($comment);
      $entityManager->flush ();

      $this->addFlash ('success', 'Commentaire supprimé');

      return $this->redirectToRoute ('admin_comment_index');
    }
}
