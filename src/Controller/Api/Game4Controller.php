<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Game4Controller extends AbstractController
{
    /**
     * @Route("/api/game/4", name="app_api_game4", methods={"GET"})
     */
    public function question(): JsonResponse
    {
        $options = $this->generateOptions();

        return $this->json(['options' => $options]);
    }

    private function randomHexColor(): string
    {
        $number = rand(0, 16777215);
        $hex = dechex($number);
        $hex = str_pad($hex, 6, '0', STR_PAD_LEFT);

        return '#' . strtoupper($hex);
    }

    private function generateOptions(int $count = 4): array
    {
        $options = [];

        while (count($options) < $count) {
            $color = $this->randomHexColor();

            if (!in_array($color, $options)) {
                $options[] = $color;
            }
        }

        return $options;
    }
}