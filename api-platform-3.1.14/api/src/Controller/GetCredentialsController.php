<?php
namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetCredentialsController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }
        return new JsonResponse([

            'email' => $user->getEmail(),
            'id' => $user->getId(),
            'firstName' => $user->firstName,
            'organisation'=> $user->organisation->name,
        ]);
    }
}
