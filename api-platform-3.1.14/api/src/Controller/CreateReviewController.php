<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CreateReviewController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    #[Route('/reviews', name: 'add_review', methods: ['POST'])]
    public function __invoke(Request $request, SerializerInterface $serializer): Review
    {
        $data = $request->getContent();
        $review = $serializer->deserialize($data, Review::class, 'json');
        $review->owner=$this->getUser();
        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $review;
    }
}
