<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RapportVetController extends AbstractController
{
    #[Route('/rapport/vet', name: 'app_rapport_vet')]
    public function index(): Response
    {
        return $this->render('rapport_vet/index.html.twig', [
            'controller_name' => 'RapportVetController',
        ]);
    }
}