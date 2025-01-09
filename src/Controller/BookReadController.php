<?php

namespace App\Controller;

use App\Entity\BookRead;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;


class BookReadController extends AbstractController
{
    #[Route('/book-read', name: 'app_book_read', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, BookRepository $bookRepository): Response
    {

        $user = $this->getUser();

        $bookRead = new BookRead();
        
        // Récupération des données du formulaire
        $bookId = $request->request->get('book');
        $description = $request->request->get('description');
        $rating = $request->request->get('rating');
        $isRead = $request->request->get('check') ? true : false;

        // Hydratation de l'entité BookRead
        $bookRead->setUserId($user->getId());
        $bookRead->setBookId($bookId);
        $bookRead->setDescription($description);
        $bookRead->setRating($rating);
        $bookRead->setRead($isRead);
        $bookRead->setCreatedAt(new \DateTime());
        $bookRead->setUpdatedAt(new \DateTime());

        // Sauvegarde en base de données
        $entityManager->persist($bookRead);
        $entityManager->flush();

        $this->addFlash('success', 'Votre lecture a été ajoutée avec succès.');

        return $this->redirectToRoute('app.home'); // Redirection vers la page de votre choix
    }
}
