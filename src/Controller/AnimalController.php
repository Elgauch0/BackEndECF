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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/animal')]
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







    #[Route('/', name: 'get_animals', methods: 'GET')]
    public function getAnimals(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $animals = $this->em->getRepository(Animal::class)->findAllWithPagination($page, $limit);
        if (!$animals) {
            throw $this->createNotFoundException('No animal found or server down');
        }
        return $this->json($animals, JsonResponse::HTTP_OK, [], ['groups' => ['animals:read']]);
    }



    #[Route('/{id}', name: 'get_Animal', methods: 'GET', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function getAnimal(Animal $animal): JsonResponse
    {

        return $this->json($animal, JsonResponse::HTTP_OK, [], ['groups' => ['animals:read']]);
    }



    #[Route('/add', name: 'add_Animal', methods: 'POST')]
    public function addAnimal(Request $request): JsonResponse
    {
        $animalDTO = $this->serializer->deserialize($request->getContent(), Animal::class, 'json');

        $animal = new Animal();
        $animal->setNom($animalDTO->getNom());
        $animal->setDescription($animalDTO->getDescription());

        $data = json_decode($request->getContent(), true);
        $idHabitat = $data['habitatId'];

        $animal->setHabitat($this->em->getRepository(Habitat::class)->find($idHabitat));

        //valider les entrées avant de persister
        $errors = $this->validator->validate($animal);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }


        $this->em->persist($animal);
        $this->em->flush();


        return $this->json(['message' => 'Animal Created'], JsonResponse::HTTP_CREATED);
    }





    #[Route('/{id}', name: 'edit_Animal', methods: 'PUT', requirements: ['id' => Requirement::POSITIVE_INT])]
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
    /**
     * { method PUT
     * "nom":"VEGETA",
     * "description":"VEGETA M9AAAWD TAHOWUA",
     * "habitatId": "1"
     *}
     * pagination:
     * /api/animal/?page=2&limit=2
     */


    #[Route('/{id}', name: 'delete_Animal', methods: 'DELETE', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function deleteAnimal(Animal $animal): JsonResponse
    {
        $this->em->remove($animal);
        $this->em->flush();
        return $this->json(['message' => 'animal removed'], JsonResponse::HTTP_NO_CONTENT);
    }
}
