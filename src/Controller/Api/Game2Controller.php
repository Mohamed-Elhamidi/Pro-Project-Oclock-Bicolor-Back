<?php

namespace App\Controller\Api;

use App\Service\ColorPalette;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Game2Controller extends AbstractController
{
    /**
     * Jeu 2 (Classify Color) : renvoie une palette de 10 nuances d'une même
     * couleur, de la plus claire à la plus foncée, que le joueur doit classer.
     * Nécessite un jeton JWT valide.
     *
     * @Route("/api/game/2", name="app_api_game2")
     */
    public function game2(): JsonResponse
    {
        // ÉTAPE 1 : tirer un nombre au hasard entre 0 et 16777215.
        // 16777215 = FFFFFF en hexadécimal, la dernière couleur possible :
        // 256 rouges x 256 verts x 256 bleus = 16 777 216 couleurs, numérotées à partir de 0.
        $randomNumber = rand(0, 16777215);

        // ÉTAPE 2 : convertir ce nombre en hexadécimal.
        // dechex() = "décimal vers hexadécimal" (ex. 255 devient "ff").
        $hexColor = dechex($randomNumber);

        // ÉTAPE 3 : compléter avec des zéros à gauche pour avoir toujours 6 caractères.
        // Sans ça, 255 donnerait "ff" au lieu de "0000ff" : ce n'est pas un code
        // couleur valide, et la palette serait calculée sur une mauvaise couleur.
        $hexColor = str_pad($hexColor, 6, '0', STR_PAD_LEFT);

        // ÉTAPE 4 : mettre en majuscules et ajouter le "#" devant (ex. "#0000FF").
        $randomColor = '#' . strtoupper($hexColor);

        // ÉTAPE 5 : créer un objet ColorPalette (voir src/Service/ColorPalette.php)
        // en lui donnant la couleur de départ.
        $color = new ColorPalette($randomColor);

        // ÉTAPE 6 : demander à cet objet de fabriquer la palette (10 nuances par défaut).
        // La flèche "->" sert à appeler une méthode d'un objet.
        $colorPalette = $color->createPalette();

        // ÉTAPE 7 : renvoyer la palette à React en JSON : {"colors": ["#...", ...]}
        return $this->json([
            "colors" => $colorPalette
        ]);
    }
    
}
