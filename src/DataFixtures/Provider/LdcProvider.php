<?php

namespace App\DataFixtures\Provider;

class LdcProvider
{
    // Taleau des 50 recettes disponibles pour les Fixtures
    private $recipe= [
        'Blancs de poulet légers au yaourt et citron vert',
        "Osso bucco de dinde",
        "Pommes de terre au four et sauce à la ciboulette",
        "Mousse express au Nutella",
        "Beignets aux pommes",
        "Courgettes farcies au poulet",
        "Quiche au chèvre et saumon",
        "Carottes rôties au miel",
        "Fondant au chocolat express sans cuisson",
        "Daube de boeuf",
        "Gratin de cabillaud et pommes de terre",
        "Gratin de riz au poulet",
        "Crêpes aux carambars",
        "Galette des rois à la clémentine",
        "Gâteau invisible aux pommes",
        "Croque-monsieur",
        "Pudding pomme brioche",
        "Taboulé libanais",
        "Burger à La Vache qui rit",
        "Gratin de pâtes au chorizo et mozzarella",
        "Flan à la vanille",
        "Croque Hawaï",
        "Aubergines farcies au poulet",
        "Bugnes",
        "Financiers à la pistache",
       "Moelleux aux pommes au micro-ondes",
        "Gâteau magique à la vanille",
        "Tarte au chocolat",
        "Guacamole",
        "Roulés à la cannelle",
        "Blancs de poulet sauce au miel et vinaigre balsamique",
        "Tiramisu léger",
        "Tomates farcies au fromage frais",
        "Verrines de Noël avocat saumon fumé",
        "Paella au poulet et au chorizo",
        "Salade de légumes grillés",
        "Canapés de concombre au fromage frais et saumon fumé",
        "Gratin provençal",
        "Courgettes au chorizo",
        "Pommes de terre au barbecue",
        "Cake au thon",
        "Roses feuilletées aux pommes",
        "Gâteau à l'eau",
        "Flan au citron",
        "Gratin de poires aux spéculoos",
        "Flammekuche - tarte flambée",
       "Lasagnes de pommes de terre",
        "Quiche ricotta épinards",
        "Blanquette de veau",
        "Gratin de chou-fleur"];

    private $categories= [
        "Recette du monde",
        "déjeuner",
        "apéritif",
        "dîner"
    ];

        /**
     * Return a random recipe
     */
    public function getRandomRecipeName()
    {
        return $this->recipe[array_rand($this->recipe)];
    }

    /**
     * Return a random category
     */
    public function getRandomRecipeCategory()
    {
        return $this->categories[array_rand($this->categories)];
    }
}