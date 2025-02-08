<?php

namespace App\Controller;

use App\Entity\Alimentation;
use App\Entity\Animal;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/alimentation')]
class AlimentationController extends AbstractController
{
    private $em;
    private $serializer;
    private $validator;
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }




    #[Route('/add', name: 'app_alimentation', methods: 'post')]
    public function index(Request $request): JsonResponse
    {

        $alimentationDTO = $this->serializer->deserialize($request->getContent(), Alimentation::class, 'json');
        $animal_ID = json_decode($request->getContent(), true)['animal_id'];
        if (!$animal_ID) {
            throw new NotFoundExceptionInterface('animal not found');
        }

        $alimentationDTO->setAnimalId($this->em->getRepository(Animal::class)->find($animal_ID));
        $alimentationDTO->setGivenAt(new \DateTimeImmutable());

        $errors = $this->validator->validate($alimentationDTO);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }

        $this->em->persist($alimentationDTO);
        $this->em->flush();
        return $this->json(['message' => 'alimentation added'], JsonResponse::HTTP_CREATED);



        /**
         * {
         * "animal_id": 1,
         * "nourriture_donnée": "Herbe",
         * "quantité": 
         * }
         */
    }
}
