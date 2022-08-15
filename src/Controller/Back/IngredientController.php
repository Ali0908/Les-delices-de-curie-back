<?php
namespace App\Controller\Back;

use App\Entity\Ingredient;
use App\Form\CommentType;
use App\Repository\IngredientRepository;
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
class IngredientController extends AbstractController
{
    /**
     * @Route("/{id}", name="app_back_ingredient_delete", methods={"POST"})
     */
    public function delete(Request $request, Ingredient $ingredient, IngredientRepository $ingredientRepository): Response
    {
        // exemple de pas le droit de supprimer après 12h00
        //$this->denyAccessUnlessGranted('Recipe_DELETE_1200', $comment);
         
                    //! Mettre en place le token

         
        if ($this->isCsrfTokenValid('delete' . $ingredient->getId(), $request->request->get('_token'))) {
            $ingredientRepository->remove($ingredient, true);

            $this->addFlash('success', $ingredient->getName() . ', supprimé.');
        }

        return $this->redirectToRoute('app_back_ingredient_index', [], Response::HTTP_SEE_OTHER);
    }
}