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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\DependencyInjection\Loader\Configurator\serializer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



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
     * @Route("/api/score", name="app_api_scores_postscore", methods={"POST"})
     */
    public function postScore(TokenStorageInterface $tokenStorage, Request $request): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();
        
        if (!$user instanceof User) {
            return $this->json(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        //Enregistrer le score
        $entityManager = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        //Gestion de l'erreur JSON invalide
        //$serializer = $this->container->get('serializer');
        //$jsonContent = $request->getContent();

        //try {
            //$user = $serializer->deserialize($jsonContent, User::class, 'json');
        //} catch (NotEncodableValueException $e) {
            // ! je gere le cas ou il ya l'erreur
        //    return $this->json(["error" => "JSON INVALID"], Response::HTTP_BAD_REQUEST);
        //}

        //Creer et rendre persistante l'entite Score

        $score = new Scores();
        //$score->setIdUser($user->getId());
        $score->setIdUser($user);
        dd($score);
        $score->setIdGame($data['id_game']);
        $score->setNbPoints($data['score']);

        

        $entityManager->persist($score);
        $entityManager->flush();

        return $this->json(["message" => "Score saved"], JsonResponse::HTTP_CREATED);

        // on ne revoit pas une vue mais un json
        //return $this->json($user);
        //return $this->json($score, JsonResponse::HTTP_CREATED);
        //return $this->json([
            //'user_id' => $user->getId(),
            //'game_id' => $user->getIdGame(),
            //'score' => $user->getNbPoints(),
            //'message' => 'Score saved',
        //], JsonResponse::HTTP_CREATED);

    }

}