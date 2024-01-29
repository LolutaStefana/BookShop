<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Organisation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CreateOrganisationController extends AbstractController
{
    public function __construct( private readonly EntityManagerInterface $entityManager)
    {
    }
    #[Route('/organisations', name: 'add_organisation', methods: ['POST'])]
    public function __invoke(Request $request, SerializerInterface $serializer): Organisation
    {
        $data = $request->getContent();
        $organisation = $serializer->deserialize($data, Organisation::class, 'json');
        $organisation->owner=$this->getUser();
        $this->entityManager->persist($organisation);
        $this->entityManager->flush();
        
        return $organisation;
    }
}
