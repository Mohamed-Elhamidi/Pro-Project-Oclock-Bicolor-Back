<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class UserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="app_admin_user_list", methods={"GET"})
     */
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('back/user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="app_admin_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="app_admin_user_edit", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {

        $form = $this->createForm(UserType::class, $user, ["custom_option" => "edit"]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_admin_user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/admin/user/add", name="app_admin_user_add", methods={"GET", "POST"})
     * @ParamConverter("user", class="App\Entity\User")
     */
    public function add(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Je pense à hasher le mot de passe
            // Je récupère le mot de passe en clair
            $plainPassword = $user->getPassword();
            // Je le hash
            $passwordHash = $passwordHasher->hashPassword($user, $plainPassword);
            // Je set le mot de passe
            $user->setPassword($passwordHash);

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_admin_user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/admin/user/{id}/delete", name="app_admin_user_delete", requirements={"id" = "\d+"}, methods={"DELETE"})
     */

    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        //  supprimer le post 
        $entityManager->remove($user);
        //  retirer en bdd la nouvelle valeur
        $entityManager->flush();

        // On ajoute un flash message
        $this->addFlash(
            'warning',
            "User has been deleted"
        );


        return $this->redirectToRoute('app_admin_user_list', [], Response::HTTP_SEE_OTHER);
    }
}
