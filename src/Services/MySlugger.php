<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger
{
    // TODO : il me faut une méthode qui prend un titre en paramètre, et qui me renvoit un slug
    // TODO : on a besoin du service SluggerInterface, mais on peut pas le demander dans notre méthode
    // on va donc le demander dans le constructeur
    
    /**
    * instance de SluggerInterface
    * @link https://symfony.com/doc/current/components/string.html#slugger
    * @var SluggerInterface
    */
    private $slugger;

    /**
    * Paramétrage du service : active le lower()
    *
    * @var bool
    */
    private $paramLower;
        
    /**
    * Constructor
    * @param string $lower viens du fichier services.yaml, dans la partie argument
    */
    public function __construct(SluggerInterface $slugger, ContainerBagInterface $params, string $lower='')
    {
        // si j'utilise l'argument $lower, j'ai directement la valeur du fichier .env
        // dump($lower);
        $this->slugger = $slugger;
        // ici je vais lire la valeur de mon paramètre dans le fichier services.yaml
        $valeurServiceYaml = $params->get('myslugger.lower');

        //! la valeur étant en string et on veut un booleen
        // on compare les valeurs textes pour en obtenir une valeur booléenne
        $this->paramLower = ($valeurServiceYaml === 'true');
    }

    /**
     * renvoit le slug d'un nom de recette
     *
     * @param string $name
     * @return string name sluggifié
     */
    // public function slug(string $name, $lower = true): string
    public function slug(string $name): string
    {  
        $slug = $this->slugger->slug($name);

        if ($this->paramLower) {
            // @link https://symfony.com/doc/current/components/string.html#methods-to-change-case
            $slug = $slug->lower();
        }

        return $slug;
    }
}