<?php
namespace App\Controller;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    private BookRepository $bookRepository;

    public function __construct(readonly EntityManagerInterface $entityManager, BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function __invoke(
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        $page = $request->query->getInt('page');
        $itemsPerPage = $request->query->getInt('itemsPerPage');
        $user=$this->getUser();
        $books = $this->bookRepository->findPaginatedBooks($page, $itemsPerPage,$user);
        $response = [
            'hydra:member' => $books,
        ];
        $normalizedResponse = $serializer->normalize($response, null, [AbstractNormalizer::GROUPS => ['book']]);
        return $this->json($normalizedResponse);
    }
}
