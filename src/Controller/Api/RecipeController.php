<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/recipe",name="api_recipe_")
 */
class RecipeController extends ApiController
{

    /**
     * @Route("/", name="browse", methods={"GET"} )
     */
    public function browse(RecipeRepository $recipe): JsonResponse
    {
        $allRecipe = $recipe->findAll();
        return $this->json($allRecipe, Response::HTTP_OK, [], ["groups" => ["api_recipe_browse"]]);
    }


    /**
     * @Route("/home", name="home", methods={"GET"})
     * @return Response
     */
    public function home(RecipeRepository $recipeRepository): Response
    {
        // // On va chercher les listes des recettes dans notre Model Recipe
        // $recipe = new Recipe();
        // $listRecipes = $recipe->getName();

        //<3 Maintenant on utilise le RecipeRepository
        $listRecipes = $recipeRepository->findAll();


        // On transmet à notre vue la liste des recettes
        return $this->json200(
            $listRecipes,
            ["groups" => ["api_recipe_home"]]
        );
    }


    /**
     * @Route("/readbyId/{id}", name="readbyId", methods={"GET"})
     */
    public function readbyId(Recipe $recipe): JsonResponse
    {


        if ($recipe === null) {
            return $this->json404("La recette n'a pas été trouvée");
        }
        return $this->json200(
            $recipe,
            ["groups" => ["api_recipe_read"]]
        );
    }


    // /**
    // * @Route("/{slug}", name="read", methods={"GET"})
    // */
    // public function read(string $slug, RecipeRepository $recipeRepository): JsonResponse
    // {
    // $recipe = $recipeRepository->findOneBy(['slug' => $slug]);

    // if ($recipe === null)
    // {
    //     return $this->json404("La recette n'a pas été trouvée");   
    // }
    // return $this->json200(
    //     $recipe,
    //     ["groups" => ["api_recipe_read"]]); 
    // }

    /**
     * Creation de Recette
     *
     * @Route("/add", name="add", methods={"POST"})
     * 
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ManagerRegistry $manager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function add(
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $manager,
        ValidatorInterface $validator
    ): JsonResponse {
        // Récupérer le contenu JSON
        $jsonContent = $request->getContent();

        $recipe = $serializer->deserialize($jsonContent, Recipe::class, 'json');

        $errorsList = $validator->validate($recipe);
        // Y'a-t-il des erreurs ?
        if (count($errorsList) > 0) {
            // TODO Retourner des erreurs de validation propres

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // On sauvegarde l'entité
        $em = $manager->getManager();
        $em->persist($recipe);
        $em->flush();

        // TODO : return 201
        return $this->json(
            $recipe,
            // je précise que tout est OK de mon coté en précisant que la création c'est bien passé
            // 201
            Response::HTTP_CREATED,
            // REST demande un header Location + URL de la ressource
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_recipe_readbyId', ['id' => $recipe->getId()])
            ],
            //! on n'oublie pas les groupes de sérialisation, même si on redirige
            [
                "groups" => "api_recipe_add"
            ]
        );
    }



    /**
     * @Route("/edit/{id}",name="edit", methods={"PUT"})
     */
    public function edit(?Recipe $recipe, Request $request, SerializerInterface $serializerInterface, ManagerRegistry $doctrine)
    {
        // gestion du paramConverter
        if ($recipe === null) {
            return $this->json404();
        }

        // je récup mon contenu JSON
        $jsonContent = $request->getContent();

        // dump($recipe);
        // @link https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        //? avec le paramètre context, on précise l'objet à mettre à jour : $recipe
        /*
        d'après la doc, le serializer n'est pas capable de desérialiser les relations/tableaux de relations
        la doc propose d'utiliser un autre normalizer.
        SAUF que nous avons notre denormalizer qui lui sait faire
        donc lors de la denormalisation d'un tableau de relation (genres) notre denormalizer prendra le relais
        */
        $serializerInterface->deserialize(
            $jsonContent,
            Recipe::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $recipe]
        );

        //! faire attention car le deserialize ne fait plus d'erreur si on lui envoit nain porte quoi.
        //! je ne sais pas pourquoi 🤷
        //dd($recipe);

        // on met à jour la BDD
        $doctrine->getManager()->flush();

        return $this->json(
            null,
            Response::HTTP_PARTIAL_CONTENT,
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_recipe_readbyId', ['id' => $recipe->getId()])
            ]
        );
    }
}
