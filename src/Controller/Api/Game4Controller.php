<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Round;
use Doctrine\ORM\EntityManagerInterface;

class Game4Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/game/4", name="app_api_game4", methods={"GET"})
     */
    public function question(): JsonResponse
    {

        $options = $this->generateOptions();
        $correctColor = $options[array_rand($options)];

        $round = new Round();
        $round->setUser($this->getUser());
        $round->setCorrectColor($correctColor);
        $round->setStartTime(new \DateTime());

        $this->entityManager->persist($round);
        $this->entityManager->flush();

        return $this->json([
            'roundId' => $round->getId(),
            'color' => $correctColor,
            'options' => $options,
        ]);
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
