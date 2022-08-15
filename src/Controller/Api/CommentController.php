<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Repository\CommentRepository;
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
 * @Route("/api/comments",name="api_comments_")
 */
class CommentController extends ApiController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(CommentRepository $repo): JsonResponse
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
                    "api_comments_browse"
                ]
            ]
        );
    }

    /**
     * @Route("/{id}",name="read", 
     *      methods={"GET"},
     *      requirements={"id"="\d+"})
     */
    public function read( $comment)
    {
        return $this->json(
            // le comment
            $comment,
            Response::HTTP_OK,
            [],
            // le contexte, on l'utilise pour spécifier les groupes de serialisation
            [
                // je lui donne le/les noms de groupes de serialisation
                "groups" =>
                [
                    "api_comments_read"
                ]
            ]
        );
    }

    /**
     * @Route("/add/{id}", methods={"POST"})
     * //@IsGranted("ROLE_MANAGER")
     *
     * @param Request $request
     * @param Repository $repo
     * @return JsonResponse
     */
    public function add(
        Request $request,
        CommentRepository $repo,
        SerializerInterface $serializerInterface,
        ValidatorInterface $validator
    ): JsonResponse {

        // on ne doit pas utiliser des méthodes qui retourne du HTML
        // $this->denyAccessUnlessGranted("ROLE_USER");
        // on teste les droits à la main
        if (!$this->isGranted("ROLE_USER")) {
            return $this->json(["error" => "Authorised user only"], Response::HTTP_FORBIDDEN);
        }

        // TODO : récuperer les infos dans le body/content de la requete
        $jsonContent = $request->getContent();
        // dd($jsonContent);
        // {"name":"le super comment"}

        // pour désérialiser il nous faut le composant de serialisation
        // on l'obtient avec le service SerializerInterface
        //! faire attention à ce que nous fournit l'utilisateur !!!!!
        try // essaye d'éxécuter ce code
        {
            /** @var Comment $newComment */
            $newComment = $serializerInterface->deserialize($jsonContent, Comment::class, 'json');
        } catch (Exception $e) // si tu n'y arrives pas
        {
            //dd($e);
            // j'arrive ici si une exception a été lancée
            // dans notre cas si le json fourni n'est pas bien écrit : en fait c'est pas du json
            return $this->json("Le JSON est mal formé", Response::HTTP_BAD_REQUEST);
        }
        //dd($new);

        // TODO : valider les infos
        //! faire attention à ce que nous fournit l'utilisateur !!!!!
        // car on n'a pas de formulaire qui nous valide tout : $form->isValid()
        $errors = $validator->validate($newComment);

        if (count($errors) > 0) {
            //dd($errors);
            // TODO : à améliorer, car illisible
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // TODO : faire l'insertion
        // on utilise la version raccourcie par le repository
        // le paramètre true, nous fait le flush() auto
        // ça correspond à persist() ET flush()
        $repo->add($newComment, true);

        // TODO : faire un retour comme quoi tout c'est bien passé
        // on fournit l'objet qui a été créé pour que notre utilisateur puisse avoir l'ID
        return $this->json(
            // le comment avec l'ID
            $newComment,
            Response::HTTP_CREATED,
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_comments_read', ['id' => $newComment->getId()])
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
        Comment $comment = null,
        Request $request,
        ManagerRegistry $doctrine,
        SerializerInterface $serializerInterface
    ): JsonResponse {
        // dd($comment);
        // gestion du paramConverter
        if ($comment === null) {
            return $this->json404();
        }

        //dump($comment);
        $jsonContent = $request->getContent();
        // dump($jsonContent);
        // @link https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        //? avec le paramètre context, on précise l'objet à mettre à jour : $comment
        //! The AbstractNormalizer::OBJECT_TO_POPULATE is only used for the top level object. 
        //! If that object is the root of a tree structure, all child elements that exist in the normalized data will be re-created with new instances.
        $serializerInterface->deserialize(
            $jsonContent,
            Comment::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $comment]
        );
        //dump($comment);

        $doctrine->getManager()->flush();

        return $this->json(
            // le comment avec l'ID
            $comment,
            Response::HTTP_PARTIAL_CONTENT,
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_comments_read', ['id' => $comment->getId()])
            ],
            [
                "groups" => "api_comments_read"
            ]
        );
    }
}
