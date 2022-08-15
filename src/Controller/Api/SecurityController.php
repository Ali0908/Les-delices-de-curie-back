<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends ApiController
{
/**
    * @Route("/api/login/", name="app_login", methods={"GET", "POST"})
    */
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
 
        // S'il y a une erreur de login
       $error = $authenticationUtils->getLastAuthenticationError();
    
        if ($error) {
            // ⚠️ Il ne faut pas renvoyer un code 404 ⚠️ 
            // Le code 404 est égal à une ressource (page) non trouvée
            
            // Il vaut mieux envoyer une erreur 401 pour un refus d'autorisation
            return $this->json403($error,Response::HTTP_FORBIDDEN,[], ["groups" => ["api_login"]]);
        }
 
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();   
 
        return $this->json($lastUsername,Response::HTTP_OK,[], ["groups" => ["api_login"]]);
    }
    // public function browse(RecipeRepository $recipe): JsonResponse
    // {
    //     $allRecipe = $recipe->findAll();
    //     return $this->json($allRecipe,Response::HTTP_OK,[], ["groups" => ["api_recipe_browse"]]);    




    /**
     * @Route("/api/logout/", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
