<?php

namespace App\Controller\Api;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/ingredient",name="api_ingredient_")
 */
class IngredientController extends ApiController
{

    /**Route to display all ingredients
     * @Route("/", name="browse", methods={"GET"} )
     */
    public function browse(IngredientRepository $ingredientRepository): JsonResponse
    {
        $allIngredient=$ingredientRepository->findAll();
        return $this->json($allIngredient,Response::HTTP_OK,[], ["groups" => ["api_ingredient_browse" ]]);     
    }

     /**Route to display a ingredient
     * @Route("/{id}", name="read", methods={"GET"})
     * requirements={"id"="\d+"})
     */
    public function read(Ingredient $ingredient=null): JsonResponse
    {

        if ($ingredient === null)
        {
            return $this->json404("La recette n'a pas été trouvée");   
        }
        return $this->json200(
            $ingredient,
            ["groups" => ["api_ingredient_read"]]); 
    }
    /**
     * Creation des ingrédients
     *
     * @Route("/add/{id}", name="add", methods={"POST"})
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

        $ingredient = $serializer->deserialize($jsonContent, Ingredient::class, 'json');

        $errorsList = $validator->validate($ingredient);
        // Y'a-t-il des erreurs ?
        if (count($errorsList) > 0) {
            // TODO Retourner des erreurs de validation propres

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // On sauvegarde l'entité
        $em = $manager->getManager();
        $em->persist($ingredient);
        $em->flush();

        // TODO : return 201
        return $this->json(
            $ingredient,
            // je précise que tout est OK de mon coté en précisant que la création c'est bien passé
            // 201
            Response::HTTP_CREATED,
            // REST demande un header Location + URL de la ressource
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_ingredient_read', ['id' => $ingredient->getId()])
            ],
            //! on n'oublie pas les groupes de sérialisation, même si on redirige
            [
                "groups" => "api_ingredient_read"
            ]
        );
    }
    /**Route to update a ingredient
     * @Route("/edit/{id}",name="edit", methods={"PUT", "PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(?Ingredient $ingredient, Request $request, SerializerInterface $serializerInterface, ManagerRegistry $doctrine)
    {
        // gestion du paramConverter
        if ($ingredient === null) {
            return $this->json404();
        }

        // je récup mon contenu JSON
        $jsonContent = $request->getContent();

        // dump($movie);
        // @link https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        //? avec le paramètre context, on précise l'objet à mettre à jour : $ingredient
        /*
        d'après la doc, le serializer n'est pas capable de desérialiser les relations/tableaux de relations
        la doc propose d'utiliser un autre normalizer.
        SAUF que nous avons notre denormalizer qui lui sait faire
        donc lors de la denormalisation d'un tableau de relation (genres) notre denormalizer prendra le relais
        */
        $serializerInterface->deserialize(
            $jsonContent,
            Ingredient::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $ingredient]
        );

        //! faire attention car le deserialize ne fait plus d'erreur si on lui envoit nain porte quoi.
        //! je ne sais pas pourquoi 🤷
        //dd($movie);

        // on met à jour la BDD
        $doctrine->getManager()->flush();

        return $this->json(
            null,
            Response::HTTP_PARTIAL_CONTENT,
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_ingredient_read', ['id' => $ingredient->getId()])
            ]
        );
    }
}
