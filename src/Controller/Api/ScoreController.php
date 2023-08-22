<?php

namespace App\Controller\Api;

use App\Repository\ScoresRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ScoreController extends AbstractController
{
    /**
     * @Route("/api/scores", name="app_api_scores_getScores", methods={"GET"})
     */
    public function getScores(ScoresRepository $scoreRepository)
    {
        // Je récupère tous les scores
        $scores = $scoreRepository->findAll();

        // Je créer une réponse en JSON des scores, le groupe "scores" permet d'éviter les références circulaires
        $response = $this->json($scores, 200, [], ['groups' => 'scores']);

        // je retourne la réponse
        return $response;
    }
}