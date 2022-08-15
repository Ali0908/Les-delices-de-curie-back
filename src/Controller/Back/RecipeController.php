<?php

namespace App\Controller\Back;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
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
 * @IsGranted("ROLE_ADMIN")
 * 
 * @Route("/back/recipe")
 */
class RecipeController extends AbstractController
{
    /**
     * @Route("/", name="app_back_recipe_index", methods={"GET"})
     */
    public function index(RecipeRepository $recipeRepository): Response
    {
        // @link https://symfony.com/doc/current/security.html#securing-controllers-and-other-code
        // si l'utilisateur courant n'a pas le role manager
        // cela lance une erreur 403
        //? si l'utilisateur n'est pas connecté => redirection vers la page de login
       $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('back/recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_recipe_new", methods={"GET", "POST"})
     */
    public function new(Request $request, RecipeRepository $recipeRepository, MySlugger $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO : utiliser le service OmdbAPI
            // $recipe->setPoster()

            // TODO : générer le slug
            //? gestion du slug dans le Listener
            //$recipeSlug = $slugger->slug($recipe->getTitle());

            // dd($recipe->getTitle(). " | " . $recipeSlug);
            // $recipe->setSlug($recipeSlug);

            $recipeRepository->add($recipe, true);

            $this->addFlash('success', 'Recette ajouté(e).');

            return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_recipe_show", methods={"GET"})
     */
    public function show(Recipe $recipe): Response
    {
        return $this->render('back/recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_back_recipe_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Recipe $recipe, RecipeRepository $recipeRepository, MySlugger $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // ! ne pas pouvoir corriger le challenge
        // $this->denyAccessUnlessGranted('RECIPE_EDIT_1400', $recipe);

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // TODO : ne pas oublier le slug si on modifie le titre
            //? gestion du slug dans le Listener
            // $recipeSlug = $slugger->slug($recipe->getTitle());
            // $recipe->setSlug($recipeSlug);

            $recipeRepository->add($recipe, true);

            $this->addFlash('success', 'Recette modifié(e).');
            return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_back_recipe_delete", methods={"POST"})
     */
    public function delete(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        // exemple de pas le droit de supprimer après 12h00
        $this->denyAccessUnlessGranted('Recipe_DELETE_1200', $recipe);

        if ($this->isCsrfTokenValid('delete' . $recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);

            $this->addFlash('success', $recipe->getName() . ', supprimé.');
        }

        return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
