<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class AnimalController extends AbstractController
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







    #[Route('/animal', name: 'get_animals', methods: 'GET')]
    public function getAnimals(): JsonResponse
    {
        $animals = $this->em->getRepository(Animal::class)->findAll();
        return $this->json($animals, JsonResponse::HTTP_OK, [], ['groups' => ['animals:read']]);
    }



    #[Route('/animal/{id}', name: 'get_Animal', methods: 'GET', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function getAnimal(Animal $animal): JsonResponse
    {
        return $this->json($animal, JsonResponse::HTTP_OK, [], ['groups' => ['animals:read']]);
    }



    #[Route('/administration/animal/add', name: 'add_Animal', methods: 'POST')]
    public function addAnimal(Request $request): JsonResponse
    {
        $animalDTO = $this->serializer->deserialize($request->getContent(), Animal::class, 'json');

        $animal = new Animal();
        $animal->setNom($animalDTO->getNom());
        $animal->setDescription($animalDTO->getDescription());

        $data = json_decode($request->getContent(), true);
        $idHabitat = $data['habitatId'];

        $animal->setHabitat($this->em->getRepository(Habitat::class)->find($idHabitat));
        $errors = $this->validator->validate($animal);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }


        $this->em->persist($animal);
        $this->em->flush();


        return $this->json(['message' => 'Animal Updated'], JsonResponse::HTTP_CREATED);
    }





    #[Route('/administration/animal/{id}', name: 'edit_Animal', methods: 'PUT', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function editAniaml(Animal $animal, Request $request): JsonResponse
    {

        $updatedAnimal = $this->serializer->deserialize(
            $request->getContent(),
            Animal::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $animal]
        );
        $errors = $this->validator->validate($animal);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }

        $this->em->flush();

        return $this->json(['message' => 'animal edited'], JsonResponse::HTTP_ACCEPTED);
    }



    #[Route('/administration/animal/{id}', name: 'delete_Animal', methods: 'DELETE', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function deleteAnimal(Animal $animal): JsonResponse
    {
        $this->em->remove($animal);
        $this->em->flush();
        return $this->json(['message' => 'animal removed'], JsonResponse::HTTP_NO_CONTENT);
    }
}
/** 
 * { method PUT
 * "nom":"VEGETA",
 * "description":"VEGETA M9AAAWD TAHOWUA",
 * "habitatId": "1"
 *}
 * pagination:
 * /api/animal/?page=2&limit=2
 */
