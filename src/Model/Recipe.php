<?php
// src/Model/Movie.php 

namespace App\Model;

class Recipe
{
    /**
     * Liste des recettes
     *
     * @var array
     */
    private $recipes = [
        

        //informations from: https://larecette.net/feuilletes-tomate-et-chevre/

        [ // index: 0
            'name' => 'Feuilletés tomate et chèvre',
           'description' => "Recette savoureuse, idéale pour vous rafraîchir l'été",
            'steps' =>" 1. Préchauffer le four à 180 – 200 °C. Découper des carrés de pâte feuilletée d’environ 10 cm de côté. Les déposer sur une plaque de cuisson recouverte de papier sulfurisé.
            2. Les badigeonner de jaune d’œuf préalablement mélangé à 1 cuillère à soupe d’eau. Enfourner pour une cuisson à blanc pendant 5 minutes.
            3. Éplucher et émincer finement l’oignon rouge puis couper les tomates en rondelles. À la sortie du four, appuyer sur le centre de chaque carré de pâte à l’aide d’une fourchette en veillant à laisser les bords intacts.
            4. Ajouter le chèvre préalablement émietté, le thym, le poivre et terminer par disposer les rondelles de tomates.
             5. Faire cuire 5 à 10 minutes en mode chaleur tournante.",
            'tags' => "",
            'categories' => 'Europe',
            'difficulties' => 'Facile',
            'comments' => '',
            'users' => "",
            'likes' => 30,
            'favorites' => 20,
            'quantity ' => "1 à 2 pâtes feuilletées
            1 bûchette de fromage de chèvre frais
            1/2 oignon blanc
            2 tomates
            Du thym séché
            Du poivre noir
            1 jaune d’œuf",
            'duration' =>" 15 minutes",
            'image' =>"https://res.cloudinary.com/hv9ssmzrz/image/fetch/c_fill,f_auto,h_488,q_auto,w_650/https://s3-eu-west-1.amazonaws.com/images-ca-1-0-1-eu/recipe_photos/original/143809/Feuillet_25C3_25A9s_2Bch_25C3_25A8vre1.jpg",
        ],
        
        [ // index :1
            'name' => 'Tacos mexicains aux crevettes',
            'description' => "Recette savoureuse réveillera vos papilles ",
             'steps' =>"1. Dans un premier temps, décortiquez et faites cuire les crevettes 4 à 5 minutes avec le cumin, le paprika et le jus de citron vert.2. Ajoutez l’ail émincé et la coriandre préalablement rincée ainsi que le sel et le poivre.3. Découpez en petits dés le poivron rouge puis faites-les revenir légèrement dans un filet d’huile d’olive.4. Pelez et découpez les avocats en cubes puis émincez le chou blanc.5. Mélangez la crème fraîche avec le jus du citron vert, salez, poivrez.5. Dans une tortilla pliée en deux, déposez un peu de crème avant d’ajouter le chou blanc, les cubes d’avocats, les poivrons, l’oignon rouge et le persil. Terminez en disposant les crevettes.",
             'tags' => "",
             'categories' => 'Amérique',
             'difficulties' => 'Moyen',
             'comments' => '',
             'users' => "",
             'likes' => 30,
             'favorites' => 20,
             'quantity ' => "6 tortillas de maïs à tacos
             18 crevettes roses
             2 c. à soupe d’huile de colza (ou tournesol)
             ½ c. à café de cumin
             ½ c. à café de paprika
             2 gousses d’ail hachées
             10 brins de persil
             2 avocats coupés en dés
             1/4 de chou blanc
             ½ oignon rouge ciselé
             ½ poivron rouge
             20 cl de crème fraîche
             2 citrons verts
             1 c. à soupe d’huile d’olive
             Sel et poivre",
             'duration' =>" 35 minutes",
             'image'=>'https://www.google.com/imgres?imgurl=https%3A%2F%2Fimages-gmi-pmc.edge-generalmills.com%2Fe3da87e1-f97f-45e5-951c-d2277a84be2b.jpg&imgrefurl=https%3A%2F%2Fwww.lifemadedelicious.ca%2Frecipes%2Frecette-de-bols-de-taco-bowlsboatsmc-aux-crevettes-mexicaines-epicees%2F26b95350-d73f-4982-b8ef-2d415c2b5b7a%3Fsc_lang%3Dfr-CA&tbnid=oQWMGBriA6Y1WM&vet=12ahUKEwiG6LP7tJP5AhUE0oUKHRSUDioQMygIegUIARDcAQ..i&docid=KQybA2tlylUSPM&w=800&h=450&q=Tacos%20mexicains%20aux%20crevettes&ved=2ahUKEwiG6LP7tJP5AhUE0oUKHRSUDioQMygIegUIARDcAQ'
        ],

    
    ];

    /**
     * Renvoie la liste des recettes
     *
     * @return array
     */
    public function getAllRecipes():array
    {
        return $this->recipes;
    }

    /**
     * renvoie les données du film dont l'id est transmis
     *
     * @param integer $id
     * @return array|null
     */
    public function getRecipe(int $id): ?array
    {

        // Quand une valeur peut être nulle, on dit qu'elle est nullable
        //ici ?array se lit tableau nullabe ou nullable array en US

        return $this->recipes[$id] ?? null;

        // ? En version non-abrégée : 
        /*
        if (array_key_exists($id, $this->recipes)) {
            return $this->recipes[$id];
        }
        else {
            return null;
        }
        */
    }

}