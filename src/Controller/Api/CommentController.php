<?php

namespace App\Controller\Api;

use App\Repository\CommentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/api/comments", name="app_api_comment_getComments", methods={"GET"})
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

}
