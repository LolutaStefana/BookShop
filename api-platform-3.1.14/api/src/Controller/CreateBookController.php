<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CreateBookController extends AbstractController
{
    public function __construct( private readonly EntityManagerInterface $entityManager)
    {
    }
    #[Route('/books', name: 'add_book', methods: ['POST'])]
    public function __invoke(Request $request, SerializerInterface $serializer): Book
    {
        $data = $request->getContent();
        $book = $serializer->deserialize($data, Book::class, 'json');
        $book->owner=$this->getUser();
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }
}
