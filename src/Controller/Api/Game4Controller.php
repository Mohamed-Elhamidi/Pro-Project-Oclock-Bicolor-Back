<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Round;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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


    // ...

    /**
     * @Route("/api/game/4/answer", name="app_api_game4_answer", methods={"POST"})
     */
    public function answer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $roundId = $data['roundId'];
        $playerAnswer = $data['answer'];

        $round = $this->entityManager->getRepository(Round::class)->find($roundId);

        if (!$round) {
            return $this->json(['error' => 'Round introuvable'], 404);
        }

        // Vérification 1 : à toi — le round appartient-il bien au joueur connecté ?
        // indice : $round->getUser() renvoie l'objet User lié, $this->getUser() le joueur connecté.
        // Compare-les avec !==
        if ($round->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Accès interdit'], 403);
        }

        // Vérification 2 : à toi — le round a-t-il déjà été répondu ?
        // indice : quelle propriété de $round est encore vide (null) tant qu'on n'a pas répondu ?
        if ($round->getEndTime() !== null) {
            return $this->json(['error' => 'Round déjà répondu'], 400);
        }

        $elapsedSeconds = (new \DateTime())->getTimestamp()
            - $round->getStartTime()->getTimestamp(); /* différence en secondes entre maintenant et $round->getStartTime() */

        $isCorrect = $playerAnswer === $round->getCorrectColor();
        $points = 0;
        if ($isCorrect) {
            $points = 1;
            if ($elapsedSeconds < 2) {
                $points += 1;
            }
        }

        $round->setEndTime(new \DateTime());
        $round->setPoints($points);

        $this->entityManager->persist($round);
        $this->entityManager->flush();

        return $this->json([
            'correct' => $isCorrect,
            'points' => $points,
        ]);
    }
}
