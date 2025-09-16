<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Wish;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CommentController extends AbstractController
{
    #[Route('/wishes/{id}/comment', name: 'comment_create', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function index(Wish $wish, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid()){
            $comment->setWish($wish);
            $comment->setUser($this->getUser());
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Comment created!');
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }
        return $this->render('comment/create.html.twig', [
            'commentForm' => $commentForm,
        ]);
    }

    #[Route('/wishes/comment/{id}/update', name: 'comment_update', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted(
        attribute: new Expression('user == comment.user'),
        subject: new Expression('args["comment"].getUser()')
    )]
    public function update(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid()){
            $em->flush();
            $this->addFlash('success', 'Comment updated!');
            return $this->redirectToRoute('wish_detail', ['id' => $comment->getWish()->getId()]);
        }
        return $this->render('comment/create.html.twig', [
            'commentForm' => $commentForm,
        ]);
    }
    
    #[Route('/wishes/comment/{id}/delete', name: 'comment_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[IsGranted(
        attribute: new Expression('user === subject or "ROLE_ADMIN" in role_names'),
        subject: new Expression('args["comment"].getUser()')
    )]
    public function delete(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        if($this->isCsrfTokenValid('delete'.$comment->getId(), $request->query->get('token'))){
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Comment deleted!');
            return $this->redirectToRoute('wish_detail', ['id' => $comment->getWish()->getId()]);
        }
        return $this->redirectToRoute('wish_list');
    }
}
