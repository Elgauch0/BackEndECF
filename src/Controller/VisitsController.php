<?php

namespace App\Controller;

use App\Document\AnimalCount;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class VisitsController extends AbstractController
{

    #[Route('/api/administration/visits', name: 'get_statistic', methods: ['GET'])]
    public function getAllAnimalCounts(DocumentManager $dm): JsonResponse
    {
        try {
            $animalsCount = $dm->getRepository(AnimalCount::class)->findAll();

            if (!$animalsCount) {
                return $this->json(['message' => 'No animal counts found'], JsonResponse::HTTP_NOT_FOUND);
            }

            return $this->json($animalsCount, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['message' => 'An error occurred', 'error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
