<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function add(Recipe $entity, bool $flush = false): void
     {
            $this->getEntityManager()->persist($entity);

     if ($flush) {
                $this->getEntityManager()->flush();
            }
     }

    public function remove(Recipe $entity, bool $flush = false): void
     {
         $this->getEntityManager()->remove($entity);

         if ($flush) {
             $this->getEntityManager()->flush();
         }
     }

    //* Query Builder

    /**
     * On veut créer une méthode qui nous renvoie tous les Recipes triés par leur titre en ordre alphabétique
     *
     * @return mixed
     */
    public function findAllRecipesByNameAscQb()
    {
        // Le querybuilder sait déjà qu'on va requêter sur l'entité Recipe
        // car nous sommes dans RecipeRepository
        // Donc pas besoin de préciser le FQCN de l'entité à requêter
        // 'm' est juste l'alias de App\Entity\Recipe
        $results = $this->createQueryBuilder('m')
            ->orderBy('m.name', 'ASC') // On trie sur la propriété creditOrder
            ->getQuery()
            ->getResult();

        return $results;
    }

    // On veut récupérer les 10 recettes les plus récents
    public function findLastestByName()
    {
        // Je crée une nouvelle instance de QueryBuilder axée sur l'entité Recipe
        $queryBuilder = $this->createQueryBuilder('m');

        // Je trie cette instance sur la prop name
        $queryBuilder = $queryBuilder->orderBy('m.name', 'DESC');

        // Je récupère que les 10 premiers résultats
        $queryBuilder = $queryBuilder->setMaxResults(10);

        // J'ai fini de façonner ma requête
        // Je crée une requête
        $query = $queryBuilder->getQuery();

        // Je récupère le résultat
        $results = $query->getResult();

        return $results;

    
          return $this->createQueryBuilder('m')
            ->orderBy('m.name', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    
    }


    public function findLatestByNameDql()
    {
        $entityManager = $this->getEntityManager();


        $query = $entityManager->createQuery('
            SELECT m FROM App\Entity\Recipe m ORDER BY m.name DESC
        ');

        return $query->setMaxResults(10)->getResult();
    }

    // public function findRandomRecipe(): Recipe
    // {
    //     // TODO : random en SQL : https://sql.sh/fonctions/rand
    //     /*
    //     $sql = "select * from recipe
    //     order by rand() LIMIT 1";

    //     $dbal = $this->getEntityManager()->getConnection();
    //     $statement = $dbal->prepare($sql);
    //     $result = $statement->executeQuery();

    //     // on récupère des données basique SQL, pas d'entité ici
    //     // on passe donc par un tableau de valeur

    //     return $result->fetchAssociative();
    //     */

    //     // autre solution : 
    //     // rajouter des extensions à doctrine : https://github.com/beberlei/DoctrineExtensions

    //     // avec le findAll() puis un random dans le tableau de résultat
    //     $allRecipe = $this->findAll();
    //     // dd($allRecipe);
    //     // @link https://www.php.net/manual/fr/function.array-rand.php
    //     $randomRecipe = $allRecipe[array_rand($allRecipe)];
    //     return $randomRecipe;
    //     /* ne fonctionne que si les ID commence à 1 et finisse à 30
    //     $id = random_int(1, 30);
    //     $recipe = $recipeRepository->find($id)
    //     */
    // }

//    /**
//     * @return Recipe[] Returns an array of Recipe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Recipe
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
