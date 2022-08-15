<?php
namespace App\Controller\Back;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Services\MySlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * 
 * @link https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/security.html#isgranted
 * @IsGranted("ROLE_MANAGER")
 * 
 * @Route("/back/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/{id}", name="app_back_comment_delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        // exemple de pas le droit de supprimer après 12h00
        //$this->denyAccessUnlessGranted('Recipe_DELETE_1200', $comment);
         
                    //! Mettre en place le token

         
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);

            $this->addFlash('success', $comment->getContentComment() . ', supprimé.');
        }

        return $this->redirectToRoute('app_back_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}