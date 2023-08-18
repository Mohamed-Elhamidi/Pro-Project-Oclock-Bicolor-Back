<?php

namespace App\Controller\Api;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

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

    /**
     * @Route("/api/users", name="app_api_user_postUser", methods={"POST"})
     */
    public function postUsers(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        // je récupere un json en brut
        $jsonContent = $request->getContent();

        // ! potentiellement j'ai une erreur si le json n'est pas bon
        // je transforme ce json en entité film
        try {
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            // ! je gere le cas ou il ya l'erreur
            return $this->json(["error" => "JSON INVALID"], Response::HTTP_BAD_REQUEST);
        }

    
        // je detecte les erreurs sur mon entité avant de la persister
        $errors = $validator->validate($user);

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
        $entityManager->persist($user);

        $entityManager->flush();

        // on ne revoit pas une vue mais un json
        return $this->json([$user], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_user", ["id" => $user->getId()])
        ], [
            "groups" => "users"
        ]);
    }

}
