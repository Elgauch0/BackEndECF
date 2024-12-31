<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/habitat')]
class HabitatController extends AbstractController
{
    #[Route('/', name: 'habitat_show')]
    public function index(): Response
    {
        return $this->render('habitat/index.html.twig', [
            'controller_name' => 'HabitatController',
        ]);
    }
}
