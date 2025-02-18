<?php

namespace App\Controller;

use App\Document\HorairePlaning;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
final class HoraireController extends AbstractController
{
    private $dm;
    private $validator;

    public function __construct(
        ValidatorInterface $validator,
        DocumentManager $dm
    ) {
        $this->validator = $validator;
        $this->dm = $dm;
    }

    #[Route('/horaire', name: 'app_horaire', methods: ['GET'])]
    public function getHoraire(): JsonResponse
    {
        $horaires = $this->dm->getRepository(HorairePlaning::class)->findAll();
        return $this->json($horaires, Response::HTTP_OK);
    }

    #[Route('/horaire', name: 'create_horaire', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {

        try {
            $data = json_decode($request->getContent(), true);

            $horaire = new HorairePlaning(
                $data['jour'] ?? '',
                $data['horaireDouverture'] ?? '08h-18H'
            );

            // Validation
            $errors = $this->validator->validate($horaire);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $this->dm->persist($horaire);
            $this->dm->flush();

            return $this->json($horaire, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Invalid request data: ' . $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/administration/horaire/{id}', name: 'update_horaire', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $horaire = $this->dm->getRepository(HorairePlaning::class)->find($id);

        if (!$horaire) {
            return $this->json(
                ['error' => 'Horaire non trouvé'],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $data = json_decode($request->getContent(), true);

            if (isset($data['jour'])) {
                $horaire->setJour($data['jour']);
            }

            if (isset($data['horaireDouverture'])) {
                $horaire->setHoraireDouverture($data['horaireDouverture']);
            }

            $errors = $this->validator->validate($horaire);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $this->dm->flush();

            return $this->json($horaire, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
