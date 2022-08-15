<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user",name="api_user_")
 */
class UserController extends ApiController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(UserRepository $userRepository): Response
    {
        $allUser = $userRepository->findAll();

        return $this->json($allUser, Response::HTTP_OK, [], ["groups" => ["api_user_browse"]]);
    }

            /**
            * @Route("/readbyId/{id}", name="readbyId", methods={"GET"})
            */
            public function readbyId( User $user): JsonResponse
            {
            

            if ($user === null)
            {
                return $this->json404("L'utlisateur n'a pas été trouvée");   
            }
            return $this->json200(
                $user,
                ["groups" => ["api_user_read"]]); 
            }


    /**
     * @Route("/add/", name="add", methods={"POST"})
     */
    public function add(
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $manager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $jsonContent = $request->getContent();
        /** @var User $user */
        $user = $serializer->deserialize($jsonContent, User::class, 'json');
        $errorsList = $validator->validate($user);
        if (count($errorsList) > 0) {
        
            $errors = (string) $errorsList;


            // 3eme version avec une méthode dans mon parent
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // On sauvegarde l'entité
        $em = $manager->getManager();
        $user->setRoles($user->getRoles());
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();

        // TODO : return 201
        return $this->json(
            $user,
            Response::HTTP_CREATED,
            // REST demande un header Location + URL de la ressource
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_user_readbyId', ['id' => $user->getId()])
            ],
            //! on n'oublie pas les groupes de sérialisation, même si on redirige
            [
                "groups" => "api_user_add"
            ]
        );
    }
}



