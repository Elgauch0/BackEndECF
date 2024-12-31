<?php

namespace App\Controller;

use App\Entity\Animal;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api')]
class AnimalController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }







    #[Route('/animal', name: 'get_animal', methods: 'GET')]
    public function show(): JsonResponse
    {
        $animals = $this->em->getRepository(Animal::class)->findAll();
        if (!$animals) {
            throw $this->createNotFoundException('No animal found or server down');
        }
        return $this->json($animals, JsonResponse::HTTP_OK, [], ['groups' => ['animals:read']]);
    }



    #[Route('/animal/{id}', name: 'get_OneAnimal', methods: 'GET', requirements: ['id' => Requirement::DIGITS])]
    public function showOne(Animal $animal): JsonResponse
    {

        return $this->json($animal, JsonResponse::HTTP_OK, [], ['groups' => ['animals:read']]);
    }
}
