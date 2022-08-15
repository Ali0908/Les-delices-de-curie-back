<?php

namespace App\DataFixtures;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixture extends Fixture implements FixtureGroupInterface
{
    private $passwordHasher;
    /**
     * Permet de faire de l'injection de dépendance
     * car la méthode load() ne l'autorise pas
     * 
     * @link https://symfony.com/doc/current/security/passwords.html#hashing-the-password
     * 
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->passwordHasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("user@user.com");
        //! NE PAS METTRE LE MOT DE PASSE EN CLAIR
        //! le hash du mot de passe doit se faire via la console
        //? bin/console security:hash-password pour la version manuelle
        // $user->setPassword("\$2y\$13\$nPUl2gNIJ0f9YfmVCojnKeuShRbfK4YDTMjFOpJtOe0D7qUrczD1u");
        //? pour la version auto
        $plaintextPassword = "user";
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        // $2y$13$g7Z1qnQUBr/3CbvxrusVLeoPtvSTXqAAVi7sU7bqFs2w1EAlKVUqq

        // ne pas oublier de rajouter un ROLE_USER
        $user->setRoles(["ROLE_USER"]);
        $user->setUser("Bob");
        $manager->persist($user);

        // ------------userAdmin-----------

        $newUserAdmin = new User();
        $plaintextPassword = "admin";
        $hashedPassword = $this->passwordHasher->hashPassword(
            $newUserAdmin,
            $plaintextPassword
        );
        $newUserAdmin->setEmail('admin@admin.com')
            ->setPassword($hashedPassword)
            //$2y$13$M9RfxabJaCacpazWKTAYgOqfY733dswpVUjwjO6Sv8hyS14aob5pm
            ->setRoles(['ROLE_ADMIN'])
            ->setUser("Bob");
        $manager->persist($newUserAdmin);


        $manager->flush();
    }

    /**
     * Nous permet de classer les fixtures pour pouvoir les éxecuter séparement
     *
     * @return array
     */
    public static function getGroups(): array
    {
        return ['userGroup'];
    }
}
