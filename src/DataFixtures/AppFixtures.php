<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $passwordHasher;
    

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager): void
    {

         // si je veux mon faker en français, je definis la langue dans le create
         $faker = Factory::create("fr_FR");
         
         
       //! USER

       for ($i = 0; $i < 3; $i++) {
       
        // J'instancie un nouvel obet user
        $admin = new User();
        // je set le nom de l'utilisateur
        $admin->setName($faker->username());
        // Je set l'email, c'est une string simple
        $admin->setEmail($faker->email());
        // Je set le password, c'est une string simple
        // J'utilise le passwordHasher passé au constructeur, ça s'appelle de l'injection de dépendance (on en parlera dans les prochains jours), il contient la méthode hashPassword qui permet de changer une string en hash par rapport à la méthode de hashage définis dans security.yaml
        // ! SI PAS DE HASH, PAS POSSIBLE DE S'AUTHENTIFIER
        // ! DE PLUS UN MDP EN CLAIR EN BDD EST A LA LIMITE DE L'ILLEGALITE
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $faker->word()));
        // Je set les roles, c'est un tableau
        $admin->setRoles($faker->word());
        // je set la date de création de l'utilisateur
        $admin->setCreatedAt(new \DateTimeImmutable($faker->date()));

        // On oublis pas de persister l'objet
        $manager->persist($admin);
    }

        $manager->flush();
    }
}
