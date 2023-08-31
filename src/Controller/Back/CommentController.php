<?php

namespace App\Controller\Back;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/admin/comment/add", name="app_admin_comment_add", methods={"GET", "POST"})
     */
    public function add(Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $commentRepository->add($comment, true);

            return $this->redirectToRoute('app_admin_comment_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/comments", name="app_admin_comment_list", methods={"GET"})
     */
    public function list(CommentRepository $CommentRepository, UserRepository $UserRepository): Response
    {
        return $this->render('back/comment/list.html.twig', [
            'comments' => $CommentRepository->findAll(),
            'users' =>$UserRepository->findAll()
        ]);
    }

    /**
     * @Route("/admin/comment/{id}", name="app_admin_comment_show", methods={"GET"})
     */
    public function show(Comment $comment, UserRepository $UserRepository, GameRepository $GameRepository): Response
    {
        return $this->render('back/comment/show.html.twig', [
            'comment' => $comment,
            'users' =>$UserRepository->findAll(),
            'games' =>$GameRepository->findAll()
        ]);
    }

     /**
     * @Route("/admin/comment/{id}/edit", name="app_admin_comment_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CommentRepository $commentRepository, Comment $comment): Response
    {
        //Je créé le formulaire de modification avec le commentaire existant
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire a été soumis et est valide

            // Enregistrer les modifications
            $commentRepository->save($comment);

            return $this->redirectToRoute('app_admin_comment_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }  

    /**
     * @Route("/admin/comment/{id}/delete", name="app_admin_comment_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }

        return $this->redirectToRoute('app_admin_comment_list', [], Response::HTTP_SEE_OTHER);
    }


}
