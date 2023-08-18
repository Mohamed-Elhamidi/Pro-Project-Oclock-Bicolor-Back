<?php

namespace App\Controller\Api;


use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="app_api_user", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        // on récupère tous les utilisateurs en BDD
        $users = $userRepository->findAll();

        
        
        return $this->json($users, Response::HTTP_OK, [], ["groups" => "users"]);
    }
}
