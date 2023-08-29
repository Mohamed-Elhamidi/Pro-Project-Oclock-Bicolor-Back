<?php

namespace App\Controller\Api;

use ColorPalette;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Game2Controller extends AbstractController
{
    /**
     * @Route("/api/game/2", name="app_api_game2")
     */
    public function game2(): JsonResponse
    {
        // permet d'obtenir une couleur aléatoire au format hexadécimal
        $randomcolor = '#' . strtoupper(dechex(rand(0, 10000000)));

        // instancie le service ColorPalette qui créer 4 couleurs du plus clair au plus foncé à partir d'une couleur qu'on lui transmets
        $color = new ColorPalette($randomcolor);

        // création de la palette de couleur
        $colorPalette = $color->createPalette();
        

        return $this->json([
            "colors" => $colorPalette
        ]);
    }
}
