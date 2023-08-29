<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Scores;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Repository\ScoresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ScoreController extends AbstractController
{
    /**
     * @Route("/api/score", name="app_api_scores_getScores", methods={"GET"})
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

    /**
     * @Route("/api/score/{id}/game", name="app_api_scores_getScoresByGame", methods={"GET"})
     */
    public function getScoresByGame(int $id, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->find($id);
        // Je récupère tous les scores
        $scores = $game->getScores();

        // si pas de scores, je renvois une 404 propre en json
        if (!$scores) {
            return $this->json(["error" => "scores inexistant"], Response::HTTP_NOT_FOUND);
        }
        // Je créer une réponse en JSON des scores, le groupe "scores" permet d'éviter les références circulaires
        $response = $this->json($scores, 200, [], ['groups' => 'gameScores']);

        // je retourne la réponse
        return $response;
    }

    /**
     * @Route("/api/score/{id}/user", name="app_api_scores_getScoresByUser", methods={"GET"})
     */
    public function getScoresByUser(int $id, UserRepository $userRepository): JsonResponse
    {
        //Je récupère les id des user
        $user = $userRepository->find($id);
        //dd($user);
        if (!$user) {
            return $this->json(["error" => "Utilisateur introuvable"], Response::HTTP_NOT_FOUND);
        }
        // Je récupère tous les scores
        $scores = $user->getScores();
        //dd($scores);
        // si pas de scores, je renvois une 404 propre en json
        if (!$scores) {
            return $this->json(["error" => "Scores introuvable"], Response::HTTP_NOT_FOUND);
        }
        // Je créer une réponse en JSON des scores, le groupe "userScores" permet d'éviter les références circulaires
        $response = $this->json($scores, 200, [], ['groups' => 'userScores']);

        // je retourne la réponse
        return $response;
    }


    /**
     * @Route("/api/score", name="app_api_scores_postscore", methods={"POST"})
     */
    public function enregistrerScore(TokenStorageInterface $tokenStorage, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupère l'utilisateur authentifié à partir du token
        $user = $tokenStorage->getToken()->getUser();

        $jsonData = $request->getContent();

        $jsonDataArray = json_decode($jsonData, true);

        $scoreSoumis = $jsonDataArray['score'];


        // Récupère l'ID du jeu soumis à partir des données JSON de la requête
        $idJeuSoumis = $jsonDataArray['id_jeu'];
        
        // Vérifie si l'utilisateur est authentifié
        if ($user) {
            // Crée une nouvelle entité Scores et associe l'utilisateur et le score
            $entiteScore = new Scores();

            //Ici, j'associe l'user actuel à l'entité Scores
            $entiteScore->setIdUser($user);
            
            //Ici, je vérifie si le score soumis est un entier valide
            if (is_numeric($scoreSoumis)) {
                //Ici, je convertis le score soumis en un entier
                $scoreInt = (int) $scoreSoumis;
                //Ici, j'associe le score à l'entité score
                $entiteScore->setNbPoints($scoreInt);

                // Récupère l'entité Game en fonction de l'id envoyé
                $jeu = $entityManager->getRepository(Game::class)->find($idJeuSoumis);

                // Vérifie si le jeu existe
                if ($jeu) {
                    // Associe l'entité Game à l'entité Scores
                    $entiteScore->setIdGame($jeu);

                    // Persiste et envoie l'entité Scores à la base de données
                    $entityManager->persist($entiteScore);
                    $entityManager->flush();

                    // Crée un tableau pour stocker les détails du score
                    $detailsScore = [
                        'utilisateur' => $user->getUserIdentifier(),
                        'score' => $scoreInt,
                        'jeu' => $jeu->getName() // Assurez-vous d'ajuster la méthode pour obtenir le nom du jeu
                    ];

                    // Retourne les détails du score sous forme de réponse JSON
                    return $this->json($detailsScore);
                } else {
                    // Si le jeu n'existe pas
                    return $this->json(['message' => 'Le jeu soumis n\'existe pas.']);
                }
            } else {
                // Si le score soumis n'est pas valide
                return $this->json(['message' => 'Le score soumis n\'est pas valide.']);
            }
        }

        // Si aucun token (utilisateur non authentifié)
        return $this->json(['message' => 'Aucun token trouvé.']);
    }

    /**
     * @Route("/api/score/{id}", name="app_api_scores_deleteScore", methods={"DELETE"})
     */
    public function deleteScore(int $id, ScoresRepository $scoresRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupère le score à supprimer en fonction de son ID
        $score = $scoresRepository->find($id);

        // Vérifie si le score existe
        if (!$score) {
            return $this->json(['message' => 'Score not found.'], Response::HTTP_NOT_FOUND);
        }

        // Supprime le score de la base de données
        $entityManager->remove($score);
        $entityManager->flush();

        return $this->json(['message' => 'Score deleted successfully.']);
    }
}
