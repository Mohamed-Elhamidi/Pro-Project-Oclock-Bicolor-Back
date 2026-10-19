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
     * @Route("/api/game/3", name="app_api_game3")
     */
    public function index(): JsonResponse
    {
        // Je crée un tableau d'options vide
        $options = [];

        // Je définis le nombre de couleurs aléatoires que je veux générer
        $numberOfColors = 4;

        for ($i = 0; $i < $numberOfColors; $i++) {
            // 1. Tirer un nombre au hasard : chaque nombre correspond à une couleur.
            $randomNumber = rand(0, 10000000);

            // 2. Le convertir en hexadécimal (ex. 255 devient "ff").
            $hexColor = dechex($randomNumber);

            // 3. Compléter avec des zéros à gauche pour avoir toujours 6 caractères.
            // Sans ça, 255 donnerait "ff", un code invalide : la bibliothèque
            // NameThatColor ne le comprend pas et répond "White" au lieu de "Blue".
            $hexColor = str_pad($hexColor, 6, '0', STR_PAD_LEFT);

            // 4. Mettre en majuscules et ajouter le "#" devant (ex. "#0000FF").
            $randomColor = '#' . strtoupper($hexColor);

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
        $nameColor = $randomColor["name"];



        return $this->json([
            "options" => $options,
            "color" => $nameColor,
            "answer" => $hexColor
        ]);
    }
}
