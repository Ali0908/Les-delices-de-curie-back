<?php

namespace App\Services;

use App\Entity\Recipe;

class AutoRating
{
    /**
     * Calcul d'un rating avant l'insertion en BDD d'un review
     *
     * @param Recipe $recipe le film concerné
     * @param integer $newRating le rating de la nouvelle critique
     * @return float le nouveau rating du film
     */
    public function totalLikes(Recipe $recipe, int $newLike): float
    {
        // TODO : j'ai besoin d'un film : injection
        // TODO : j'ai besoin de toutes les reviews : relation dans l'objet $movie
        $allLikes = $recipe->getLikes();

        // TODO : le calcul : somme de toutes les ratings de toutes les review 
        $totalLikes = 0;
        foreach ($allLikes as $like)
        {
            // je récupère le rating
            $like = $recipe->getLikes();
            // je l'ajoute au total
            $totalLikes += $newLike;
        }
        // TODO : renvoyer le résultat du calcul
        return $totalLikes;
    }
}