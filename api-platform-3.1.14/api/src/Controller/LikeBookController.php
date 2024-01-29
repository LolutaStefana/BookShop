<?php
// src/Controller/UserBookInteractionController.php
namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LikeBookController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/books/{id}/like', name: 'like_book', methods: ['POST'])]
    public function __invoke(Book $book, Request $request): JsonResponse
    {
            if ($request->getMethod() === 'POST') {
                $this->getUser()->likeBook($book);
            }
            $this->entityManager->persist($book);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Action performed successfully.']);

    }
}
