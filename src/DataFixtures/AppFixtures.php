<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Game;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Comment;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\Provider\AppProvider;
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
        // ici j'ajoute mon provider personnalisé
        $faker->addProvider(new AppProvider());


        //! USER

        for ($i = 0; $i < 2; $i++) {

            // j'utilise mon provider pour récupérer un user unique
            $dataUser = $faker->unique()->user();
            // J'instancie un nouvel obet user
            $admin = new User();
            // je set le nom de l'utilisateur
            $admin->setName($dataUser["name"]);
            // Je set l'email, c'est une string simple
            $admin->setEmail($dataUser["email"]);
            // Je set le password, c'est une string simple
            // J'utilise le passwordHasher passé au constructeur, ça s'appelle de l'injection de dépendance (on en parlera dans les prochains jours), 
            // il contient la méthode hashPassword qui permet de changer une string en hash par rapport à la méthode de hashage définis dans security.yaml
            // ! SI PAS DE HASH, PAS POSSIBLE DE S'AUTHENTIFIER
            // ! DE PLUS UN MDP EN CLAIR EN BDD EST A LA LIMITE DE L'ILLEGALITE
            $admin->setPassword($this->passwordHasher->hashPassword($admin, $dataUser["password"]));
            // Je set les roles, c'est un tableau
            $admin->setRoles($dataUser["roles"]);


            // On oublis pas de persister l'objet
            $manager->persist($admin);
        }



        //! GAMES
       
        foreach ($faker->games() as $dataGame) {
            // J'instancie un nouvel objet game
            $game = new Game();
            // je set le nom du game
            $game->setName($dataGame["name"]);
            // je set la date de création du jeu
            $game->setCreatedAt(new \DateTimeImmutable($faker->date()));
            // je persist l'objet en BDD
            $manager->persist($game);
        }
        
        // ! COMMENT
        for ($m = 0; $m < mt_rand(0, 5); $m++) {
            $comment = new Comment();

            $comment->setContent($faker->text());
            $comment->setCreatedAt(new DateTimeImmutable($faker->date()));
            $comment->setupdatedAt(new DateTimeImmutable($faker->date()));

            
            
            $comment->setIdGame($game);
            $comment->setIdUser($admin);

            $manager->persist($comment);
        }
        $manager->flush();
    }
}