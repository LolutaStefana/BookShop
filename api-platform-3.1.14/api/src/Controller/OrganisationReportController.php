<?php
// src/Controller/OrganisationReportController.php
namespace App\Controller;

use App\Entity\Organisation;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class OrganisationReportController extends AbstractController
{
    #[Route('/organisation/{id}/report', name: 'organisation_report', methods: ['GET'])]
    public function __invoke(Organisation $organisation): JsonResponse
    {
        $numberOfLikedBooks = 0;


        foreach ($organisation->users as $user) {
            if ($user instanceof User) {
                $numberOfLikedBooks += count($user->likedBooks);
            }
        }
        if($this->getUser()!=$organisation->owner)
        {
            return new JsonResponse(['message' => 'You must be the organisation owner in order to see this report'], JsonResponse::HTTP_FORBIDDEN);
        }

        return $this->json([
            'organisation' => $organisation->name,
            'number_of_liked_books' => $numberOfLikedBooks,
        ]);
    }
}


