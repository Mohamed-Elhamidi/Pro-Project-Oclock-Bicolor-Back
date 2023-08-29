<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use ourcodeworld\NameThatColor\ColorInterpreter as NameThatColor;

// pour installer le bundle NameThatColor = composer require ourcodeworld/name-that-color:dev-master  
// :dev-master car le créateur du bundle n'a pas mis en ligne une version stable

class Game3Controller extends AbstractController
{
    /**
     * @Route("/api/game3", name="app_api_game3")
     */
    public function index(): JsonResponse
    {
        // Je crée un tableau d'options vide
        $options = [];

        // Je définis le nombre de couleurs aléatoires que je veux générer
        $numberOfColors = 4;

        for ($i = 0; $i < $numberOfColors; $i++) {
            //permet d'obtenir une couleur hexadécimal aléatoire
            $randomColor = '#' . strtoupper(dechex(rand(0, 10000000)));

            // nouvelle instance du bundle NameThatColor
            $instance = new NameThatColor();

            //génère un tableau avec une couleur aléatoire au format hexadécimal et son nom
            $result = $instance->name($randomColor);

            // j'ajoute le tableau de la couleur dans le tableau d'options
            $options[] = $result;
        }
        
        // Je récupère une couleur dans le tableau options pour les valeurs color et answer du jeu
        $randomColor = $options[array_rand($options)];
        // je récupère uniquement le format hexadecimal pour l'ajouter au json
        $hexColor = $randomColor["hex"];
        


        return $this->json([
            "options" => $options,
            "color" => $hexColor,
            "answer" => $hexColor
        ]);
    }
}
