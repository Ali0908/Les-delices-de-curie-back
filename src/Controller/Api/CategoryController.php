<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/api/categories",name="api_categories_")
 */
class CategoryController extends ApiController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(CategoryRepository $repo): JsonResponse
    {
        $all = $repo->findAll();

        return $this->json(
            // data
            $all,
            // code HTTP pour dire que tout se passe bien (200) 
            Response::HTTP_OK,
            // les entêtes HTTP, on les utilise dans très peu de cas, donc valeur par défaut : []
            [],
            // le contexte, on l'utilise pour spécifier les groupes de serialisation
            [
                // je lui donne le/les noms de groupes de serialisation
                "groups" =>
                [
                    "api_categories_browse"
                ]
            ]
        );
    }

    /**
     * @Route("/{id}",name="read", 
     *      methods={"GET"},
     *      requirements={"id"="\d+"})
     */
    public function read(Category $category)
    {
        return $this->json(
            // la catégorie
            $category,
            Response::HTTP_OK,
            [],
            // le contexte, on l'utilise pour spécifier les groupes de serialisation
            [
                // je lui donne le/les noms de groupes de serialisation
                "groups" =>
                [
                    "api_categories_read"
                ]
            ]
        );
    }

    /**
     * @Route("/add/{id}",name="add", methods={"POST"})
     *
     * @param Request $request
     * @param GenreRepository $repo
     * @return JsonResponse
     */
    public function add(
        Request $request,
        CategoryRepository $repo,
        SerializerInterface $serializerInterface,
        ValidatorInterface $validator
        ): JsonResponse {

        // on ne doit pas utiliser des méthodes qui retourne du HTML
        // $this->denyAccessUnlessGranted("ROLE_ADMIN");
        // on teste les droits à la main
        if (!$this->isGranted("ROLE_ADMIN")) {
            return $this->json(["error" => "Authorised user only"], Response::HTTP_FORBIDDEN);
        }

        // TODO : récuperer les infos dans le body/content de la requete
        $jsonContent = $request->getContent();
        // dd($jsonContent);
        // {"name":"le super genre"}

        // pour désérialiser il nous faut le composant de serialisation
        // on l'obtient avec le service SerializerInterface
        //! faire attention à ce que nous fournit l'utilisateur !!!!!
        try // essaye d'éxécuter ce code
        {
            /** @var Category $newCategory */
            $newCategory = $serializerInterface->deserialize($jsonContent, Category::class, 'json');
        } catch (Exception $e) // si tu n'y arrives pas
        {
            //dd($e);
            // j'arrive ici si une exception a été lancée
            // dans notre cas si le json fourni n'est pas bien écrit : en fait c'est pas du json
            return $this->json("Le JSON est mal formé", Response::HTTP_BAD_REQUEST);
        }
        //dd($newCategory);

        // TODO : valider les infos
        //! faire attention à ce que nous fournit l'utilisateur !!!!!
        // car on n'a pas de formulaire qui nous valide tout : $form->isValid()
        $errors = $validator->validate($newCategory);

        if (count($errors) > 0) {
            //dd($errors);
            // TODO : à améliorer, car illisible
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // TODO : faire l'insertion
        // on utilise la version raccourcie par le repository
        // le paramètre true, nous fait le flush() auto
        // ça correspond à persist() ET flush()
        $repo->add($newCategory, true);

        // TODO : faire un retour comme quoi tout c'est bien passé
        // on fournit l'objet qui a été créé pour que notre utilisateur puisse avoir l'ID
        return $this->json(
            // la catégorie avec l'ID
            $newCategory,
            Response::HTTP_CREATED,
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_categories_read', ['id' => $newCategory->getId()])
            ]
            // TODO : rajouter des groupes de sérialisation
        );
    }

    /**
     * @Route("/edit/{id}",name="edit", 
     *      methods={"PUT", "PATCH"},
     *      requirements={"id"="\d+"})
     */
    public function edit(
        Category $category = null,
        Request $request,
        ManagerRegistry $doctrine,
        SerializerInterface $serializerInterface
    ): JsonResponse {
        // dd($category);
        // gestion du paramConverter
        if ($category === null) {
            return $this->json404();
        }

        //dump($category);
        $jsonContent = $request->getContent();
        // dump($jsonContent);
        // @link https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        //? avec le paramètre context, on précise l'objet à mettre à jour : $category
        //! The AbstractNormalizer::OBJECT_TO_POPULATE is only used for the top level object. 
        //! If that object is the root of a tree structure, all child elements that exist in the normalized data will be re-created with new instances.
        $serializerInterface->deserialize(
            $jsonContent,
            Category::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $category]
        );
        //dump($category);

        $doctrine->getManager()->flush();

        return $this->json(
            // la catégorie avec l'ID
            $category,
            Response::HTTP_PARTIAL_CONTENT,
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_categories_read', ['id' => $category->getId()])
            ],
            [
                "groups" => "api_categories_read"
            ]
        );
    }

    /**
     * @Route("/delete/{id}",name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Category $category
     */
    public function delete(Category $category = null, CategoryRepository $repo)
    {
        // gestion du paramConverter
        if ($category === null) {
            return $this->json404();
        }

        // je supprime tout simplement
        $repo->remove($category, true);

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('capicategories_browse')
            ]
        );
    }
}
