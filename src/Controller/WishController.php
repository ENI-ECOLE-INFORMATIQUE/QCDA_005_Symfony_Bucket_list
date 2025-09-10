<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WishController extends AbstractController
{
    #[Route('/wishes', name: 'wish_list', methods: ['GET'] )]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy(['published' => true], ['dateCreated' => 'DESC']);
        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes
        ]);
    }

    #[Route('/wishes/{id}', name: 'wish_detail', requirements: ['id'=>'\d+'],methods: ['GET'])]
    public function detail(int $id,WishRepository $wishRepository): Response
    {
        //Récupère ce wish en fonction de l'id présent dans l'URL
        $wish = $wishRepository->find($id);
        //Erreur 404 s'il n'existe pas
        if(!$wish){
            throw $this->createNotFoundException('Wish not found');
        }
        return $this->render('wish/detail.html.twig',
            ["wish" => $wish]
        );
    }
    #[Route('/wishes/create', name: 'wish_create',methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);
        if($wishForm->isSubmitted() && $wishForm->isValid()){
            $wish->setPublished(true);
            $em->persist($wish);
            $em->flush();
            $this->addFlash('success', 'Wish created!');
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/create.html.twig',
            ["wishForm" => $wishForm]
        );
    }

    #[Route('/wishes/{id}/update', name: 'wish_update',requirements: ['id'=>'\d+'], methods: ['GET','POST'])]
    public function update(int $id,WishRepository $wishRepository, $request, EntityManagerInterface $em): Response
    {
        $wish = $wishRepository->find($id);
        if(!$wish){
            throw $this->createNotFoundException('Wish not found, Sorry !');
        }

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);
        if($wishForm->isSubmitted() && $wishForm->isValid()){
            $wish->setDateUpdated(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Wish updated!');
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/create.html.twig',
            ["wishForm" => $wishForm]
        );
    }

    #[Route('/wishes/{id}/delete', name: 'wish_delete',requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function delete(int $id,WishRepository $wishRepository, $request): Response
    {
        $wish = $wishRepository->find($id);
        if (!$wish) {
            throw $this->createNotFoundException('Wish not found, Sorry !');
        }
        if($this->isCsrfTokenValid('delete'.$wish->getId(), $request->request->get('token'))){
            $wishRepository->remove($wish,true);
            $this->addFlash('success', 'Wish deleted!');
        }else{
            $this->addFlash('danger', 'Sorry, this Wish cannot be deleted!');
        }
        return $this->redirectToRoute('wish_list');
    }


}
