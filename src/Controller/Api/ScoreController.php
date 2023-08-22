<?php

namespace App\Controller\Api;

use App\Entity\Scores;
use App\Entity\Game;
use App\Entity\User;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;


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

    /**
     * @Route("/api/games/{id}/scores", name="app_api_scores_getScoresByGame", methods={"GET"})
     */
    public function getScoresByGame(int $id, GameRepository $gameRepository ): JsonResponse
    {
        $game = $gameRepository->find($id);
        // Je récupère tous les scores
        $scores = $game->getScores();

        // si pas de scores, je renvois une 404 propre en json
        if(!$scores){
            return $this->json(["error" => "scores inexistant"], Response::HTTP_NOT_FOUND);
        }
        // Je créer une réponse en JSON des scores, le groupe "scores" permet d'éviter les références circulaires
        $response = $this->json($scores, 200, [], ['groups' => 'gameScores']);

        // je retourne la réponse
        return $response;
    }

    /**
     * @Route("/api/user/{id}/scores", name="app_api_scores_getScoresByUser", methods={"GET"})
     */
    public function getScoresByUser(int $id, UserRepository $userRepository ): JsonResponse
    {
        //Je récupère les id des user
        $user = $userRepository->find($id);
        
        if(!$user)
        {
            return $this->json(["error" => "Utilisateur introuvable"], Response::HTTP_NOT_FOUND);
        }
        // Je récupère tous les scores
        $scores = $user->getScores();

        // si pas de scores, je renvois une 404 propre en json
        if(!$scores)
        {
            return $this->json(["error" => "Scores introuvable"], Response::HTTP_NOT_FOUND);
        }
        // Je créer une réponse en JSON des scores, le groupe "scores" permet d'éviter les références circulaires
        $response = $this->json($scores, 200, [], ['groups' => 'userScores']);

        // je retourne la réponse
        return $response;
    }

        /**
     * @Route("/api/scores", name="app_api_scores_postscores", methods={"POST"})
     */
    public function postScores(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        // je récupere un json en brut
        $jsonContent = $request->getContent();

        // ! potentiellement j'ai une erreur si le json n'est pas bon
        // je transforme ce json en entité score
        try {
            $scores = $serializer->deserialize($jsonContent, Scores::class, 'json');
        } catch (NotEncodableValueException $e) {
            // ! je gere le cas ou il ya l'erreur
            return $this->json(["error" => "JSON INVALIDE"], Response::HTTP_BAD_REQUEST);
        }

        // je detecte les erreurs sur mon entité avant de la persister
        $errors = $validator->validate($scores);

        // on renvoi un json avec les erreurs
        if (count($errors) > 0) {

            // je crée un nouveau tableau d'erreur
            $dataErrors = [];

            foreach ($errors as $error) {
                // j'injecte dans le tableau à l'index de l'input, les messages d'erreurs qui concernent l'erreur en question
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            // je retourne le json avec mes erreurs
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // on oublie pas de persist et de flush
        $entityManager->persist($scores);

        $entityManager->flush();

        // on ne revoit pas une vue mais un json
        return $this->json($scores, Response::HTTP_CREATED, [], ['groups' => 'scores']);
    }

}