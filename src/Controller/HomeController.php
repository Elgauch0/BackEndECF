<?php

namespace App\Controller;

use App\Entity\Habitat;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $manager): JsonResponse
    {
        $habitats =  $manager->getRepository(Habitat::class)->findAll();;

        return $this->json($habitats);
    }
}
