<?php

namespace App\Controller\Api;

use App\Entity\Game; 
use App\Entity\Comment;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentController extends AbstractController
{
     /**
     * @Route("/api/comment/add", name="app_api_comment_add", methods={"POST"})
     */
    public function add(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        
        // Récupère l'utilisateur authentifié à partir du token
        $user = $tokenStorage->getToken()->getUser();

        // je récupere un json en brut
        $jsonContent = $request->getContent();
        $jsonDataArray = json_decode($jsonContent, true);
        $idJeuSoumis = $jsonDataArray['id_game'];
      
        // ! potentiellement j'ai une erreur si le json n'est pas bon
        // je transforme ce json en entité comment
        try {
            $comment = $serializer->deserialize($jsonContent, Comment::class, 'json');

            $comment->setCreatedAt(new \DateTimeImmutable());

            $comment->setIdUser($user);

            $jeu = $entityManager->getRepository(Game::class)->find($idJeuSoumis);

            $comment->setIdGame($jeu);

            
        
        } catch (NotEncodableValueException $e) {
            // ! je gere le cas ou il ya l'erreur
            return $this->json(["error" => "JSON INVALID"], Response::HTTP_BAD_REQUEST);
        }
        
        // je detecte les erreurs sur mon entité avant de la persister
        $errors = $validator->validate($comment);

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
        $entityManager->persist($comment);
        $entityManager->flush();

        // on ne renvoit pas une vue mais un json
        return $this->json([$comment], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_comment_add", ["id" => $comment->getId()])
        ], [
            "groups" => "comments"
        ]);
    }
    
    /**
     * @Route("/api/comment", name="app_api_comment_getComments", methods={"GET"})
     */
    public function getComments (CommentRepository $commentRepository)
    {
        // Je récupère tous les scores
        $comments = $commentRepository->findAll();

        // Je créer une réponse en JSON des scores, le groupe "scores" permet d'éviter les références circulaires
        $response = $this->json($comments, 200, [], ['groups' => 'comments']);

        // je retourne la réponse
        return $response;
    }

    /**
     * @Route("/api/comment/{id}/game", name="app_api_comment_getScoresByGame", methods={"GET"})
     */
    public function getCommentsByGame(int $id, GameRepository $gameRepository, CommentRepository $commentRepository): JsonResponse
    {
        $game = $gameRepository->find($id);
        // Je récupère tous les scores
        $comments = $game->getComments();

        // si pas de scores, je renvois une 404 propre en json
        
        if (!$comments) {
            return $this->json(["error" => "No comments for this game"], Response::HTTP_NOT_FOUND);
        }
        // Je créer une réponse en JSON des scores, le groupe "scores" permet d'éviter les références circulaires
        $response = $this->json($comments, 200, [], ['groups' => 'comments']);

        // je retourne la réponse
        return $response;
    }

    /**
     * @Route("/api/comment/{id}/user", name="app_api_comment_user", methods={"GET"})
     */
    public function getCommentByUser(int $id, UserRepository $userRepository, CommentRepository $commentRepository): JsonResponse
    {
        $user = $userRepository->find($id);
        
        // Je récupère le commentaire utilisateur
        $comments = $commentRepository->find($id);

        
        if (!$user) {
            return $this->json(["error" => "User not found"], Response::HTTP_NOT_FOUND);
        }

        if (!$comments) {
            return $this->json(["error" => "Not comments for this user"], Response::HTTP_NOT_FOUND);
        }
        // Je créer une réponse en JSON des scores, le groupe "scores" permet d'éviter les références circulaires
        $response = $this->json($comments, 200, [], ['groups' => 'comments']);

        // je retourne la réponse
        return $response;
    }

    /**
     * @Route("/api/comment/{id}", name="app_api_comment_delete", methods={"DELETE"})
     */
    public function deleteComment(int $id, CommentRepository $commentRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $comment = $commentRepository->find($id);

        if (!$comment) {
            return $this->json(["error" => "Comment not found"], Response::HTTP_NOT_FOUND);
        }

        // Supprimer le comentaire
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->json(["message" => "Comment deleted successfully"], Response::HTTP_OK);
    }

    /**
     * @Route("/api/{id}/comment", name="app_api_comment_update", methods={"PATCH"})
     */
    public function updateComment(int $id,Request $request,SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, CommentRepository $commentRepository, TokenStorageInterface $tokenStorage, GameRepository $gameRepository): Response 
    {
        // Récupère l'utilisateur authentifié à partir du token
        $user = $tokenStorage->getToken()->getUser();

        // Trouver le commentaire à mettre à jour
        $comment = $commentRepository->find($id);

        if (!$comment) {
            return $this->json(["error" => "Comment not found"], Response::HTTP_NOT_FOUND);
        }

        // Récupérer le JSON à partir de la requête
        $jsonContent = $request->getContent();

        try {
            // Désérialiser le JSON dans l'entité Comment
            $updatedComment = $serializer->deserialize($jsonContent, Comment::class, 'json', ['object_to_populate' => $comment]);
            
            // Mettre à jour la date de mise à jour
            $updatedComment->setUpdatedAt(new \DateTimeImmutable());

            // Mettre à jour l'utilisateur et le jeu associés
            $updatedComment->setIdUser($user);

            $jsonDataArray = json_decode($jsonContent, true);
            $idJeuSoumis = $jsonDataArray['id_game'];
            $jeu = $entityManager->getRepository(Game::class)->find($idJeuSoumis);
            //dd($jeu);
            $updatedComment->setIdGame($jeu);

        } catch (NotEncodableValueException $e) {
            return $this->json(["error" => "JSON INVALID"], Response::HTTP_BAD_REQUEST);
        }

        // Valider l'entité Comment mise à jour
        $errors = $validator->validate($updatedComment);

        if (count($errors) > 0) {
            $dataErrors = [];

            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Persist et flush pour enregistrer les modifications
        $entityManager->persist($updatedComment);
        $entityManager->flush();

        return $this->json([$updatedComment], Response::HTTP_OK, [], ["groups" => "comments"]);
    }
}
