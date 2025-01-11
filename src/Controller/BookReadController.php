<?php

namespace App\Controller;

use App\Entity\BookRead;
use App\Repository\BookRepository;
use App\Repository\BookReadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookReadController extends AbstractController
{
    private BookReadRepository $bookReadRepository;
    private BookRepository $bookRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(BookReadRepository $bookReadRepository, BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/book-read', name: 'app_book_read', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un livre.');
            return $this->redirectToRoute('app_login');
        }

        $bookId = $request->request->get('book_id');

        if (empty($bookId)) {
            $this->addFlash('error', 'L\'ID du livre est requis.');
            return $this->redirectToRoute('app.home');
        }

        $book = $this->bookRepository->find($bookId);
        
        if (!$book) {
            $this->addFlash('error', 'Le livre sélectionné n\'existe pas.');
            return $this->redirectToRoute('app.home');
        }

        $bookRead = new BookRead();
        $bookRead->setUserId($user->getId());  
        $bookRead->setBookId($bookId); 
        $bookRead->setIsRead(true); 
        $bookRead->setCreatedAt(new \DateTime()); 
        $bookRead->setUpdatedAt(new \DateTime()); 

        $rating = $request->request->get('rating');
        $description = $request->request->get('description');
        if ($rating) {
            $bookRead->setRating($rating);
        }
        if ($description) {
            $bookRead->setDescription($description);
        }

        $this->entityManager->persist($bookRead);
        $this->entityManager->flush();

        $this->addFlash('success', 'Livre ajouté avec succès à vos lectures.');

        return $this->redirectToRoute('app.home');
    }
}

