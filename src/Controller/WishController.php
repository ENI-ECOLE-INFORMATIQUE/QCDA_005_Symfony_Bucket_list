<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\FilterCategoryType;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wishes',name: 'wish_')]
final class WishController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET','POST'] )]
    public function list(WishRepository $wishRepository,Request $request): Response
    {
        $categoryForm = $this->createForm(FilterCategoryType::class);
        $categoryForm->handleRequest($request);
        $wishes=[];

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $chosenCategory = $categoryForm->get('category')->getData();
            dump($chosenCategory);
            if($chosenCategory) {
                $wishes = $wishRepository->findBy(['category' => $chosenCategory]);
            }else{
                $wishes = $wishRepository->findBy(['published' => true], ['dateCreated' => 'DESC']);
            }

        }else{
            $wishes = $wishRepository->findBy(['published' => true], ['dateCreated' => 'DESC']);
        }
        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,
            'categoryForm' => $categoryForm,
        ]);
    }

    #[Route('/{id}', name: 'detail', requirements: ['id'=>'\d+'],methods: ['GET'])]
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

    /**
     * @throws \Exception
     */
    #[Route('/create', name: 'create',methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);
        if($wishForm->isSubmitted() && $wishForm->isValid()){
            $wish->setPublished(true);
            //Traitement de l'image
            /** @var @var UploadFile $imageFile */
            $imageFile = $wishForm->get('image')->getData();
            if($imageFile){
                $wish->setFilename($fileUploader->upload($imageFile));
            }
            // Set Current User as Author
            $wish->setAuthor($this->getUser());

            $em->persist($wish);
            $em->flush();
            $this->addFlash('success', 'Wish created!');
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/create.html.twig',
            ["wishForm" => $wishForm]
        );
    }

    #[Route('/{id}/update', name: 'update',requirements: ['id'=>'\d+'], methods: ['GET','POST'])]
    public function update(Wish $wish, Request $request,
                           EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        if(!$wish){
            throw $this->createNotFoundException('Wish not found, Sorry !');
        }

        if($wish->getAuthor() !== $this->getUser()){
            throw $this->createAccessDeniedException('You are not the author of this wish');
        }

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);
        if($wishForm->isSubmitted() && $wishForm->isValid()){
            $wish->setDateUpdated(new \DateTimeImmutable());
            //Traitement de l'image
            /** @var @var UploadFile $imageFile */
            $imageFile = $wishForm->get('image')->getData();
            if(($wishForm->has('deleteImage') && $wishForm['deleteImage']->getData())
                || $imageFile){
                //Suppression de l'ancienne image
                //Si on a coché l'option dans le formulaire ou si on change d'image.
                $fileUploader->delete($wish->getFilename(),$this->getParameter('app.images_wish_directory'));
                if($imageFile){
                    $wish->setFilename($fileUploader->upload($imageFile));
                }else{
                    $wish->setFilename(null);
                }

            }
            $em->flush();
            $this->addFlash('success', 'Wish updated!');
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/create.html.twig',
            ["wishForm" => $wishForm]
        );
    }

    #[Route('/{id}/delete', name: 'delete',requirements: ['id'=>'\d+'], methods: ['GET'])]
    #[IsGranted(['ROLE_ADMIN', 'ROLE_USER'], message: 'You are not the admin or the author of this wish')]
    public function delete(int $id,WishRepository $wishRepository,Request $request): Response
    {
        $wish = $wishRepository->find($id);
        if (!$wish) {
            throw $this->createNotFoundException('Wish not found, Sorry !');
        }
        
        // if(!$this->isGranted('ROLE_ADMIN')){
        //     throw $this->createAccessDeniedException('You are not the admin of this wish');
        // }

        if(!($wish->getAuthor() === $this->getUser()) || !$this->isGranted('ROLE_ADMIN')){
            throw $this->createAccessDeniedException('You are not the admin or the author of this wish');
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
