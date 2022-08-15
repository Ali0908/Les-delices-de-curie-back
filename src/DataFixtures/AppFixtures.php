<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\Provider\LdcProvider;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
          // Notre data Provider
          $ldcProvider = new LdcProvider();

          // Instanciation de l'usine de Faker
          $faker = Faker\Factory::create('fr_FR');
        
        //# Créons des recettes factices pour notre BDD

        $recipesList = [];
        
        for ($i = 1; $i <= 10; $i++) {
        $foodishApiCategories = ['pasta', 'rice', 'pizza', 'biryani', 'burger','butter-chicken', 'dessert', 'dosa','idly','samosa'];

       
            $recipe = new Recipe();

            $category = $foodishApiCategories[array_rand($foodishApiCategories)];

            $recipe->setName($ldcProvider->getRandomRecipeName());
            $recipe->setDescription($faker->realText(300));
            $recipe->setSteps($faker->realText(400));
            $recipe->setDuration(mt_rand(5, 240));
            $recipe->setImage(sprintf('https://foodish-api.herokuapp.com/images/%s/%s%s.jpg', $category, $category, random_int(1,22)));
            // sprintf permet de remplacer la concaténation, %s: représente un argument, le premier $category, le deuxième $category et le dernier c'est random_int...
            $recipe->setLikes(mt_rand(0, 50) );

            
            $recipesList[] = $recipe;
            //array_push($recipesList, $recipe);

            $manager->persist($recipe);
        }

        $categoriesList = [];

        // Je veux créer 4 catégories 
        for ($k = 1; $k <= 4; $k++) {
            $category = new Category();
            $category->setName ($ldcProvider->getRandomRecipeCategory());

            // On l'ajoute à la liste pour usage ultérieur
            // en effet, il va falloir lier les genres et les movies entre eux
            $categoriesList[] = $category;

            // ça demande au manager repository de Doctrine de prendre en compte cet objet pour le prochain flush
            $manager->persist($category);
        }
            $manager->flush();
    }
}