<?php

namespace App\Controller;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_home', methods: ['Get'])]
    public function Users(EntityManagerInterface $manager): JsonResponse
    {
        $users =  $manager->getRepository(User::class)->findAll();;

        return $this->json($users, JsonResponse::HTTP_OK, [], ['groups' => ['users:read']]);
    }
}
