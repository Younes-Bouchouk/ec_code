<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\BookReadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    private BookReadRepository $bookReadRepository;
    private BookRepository $bookRepository;

    public function __construct(BookReadRepository $bookReadRepository, BookRepository $bookRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
    }

    #[Route('/', name: 'app.home')]
    #[IsGranted('ROLE_USER')]  

    public function index(): Response
    {
        $user = $this->getUser();
        $userId = $user ? $user->getId() : null;
        $booksRead = $this->bookReadRepository->findByUserId($userId, false);

        // Ajouter les informations du livre (nom, etc.) pour chaque lecture
        $booksWithDetails = [];
        foreach ($booksRead as $bookRead) {
            $book = $this->bookRepository->find($bookRead->getBookId());
            $booksWithDetails[] = [
                'bookRead' => $bookRead,
                'book' => $book, // Détails du livre (nom, etc.)
            ];
        }

        //return $this->redirectToRoute('app.home');

        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'booksWithDetails' => $booksWithDetails,
            'name' => 'Accueil',
            'userId' => $userId,
        ]);
    }

}
