<?php

namespace App\Controller;

use App\Entity\Animal;
use DateTimeImmutable;
use App\Entity\RapportVeterinaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/rapport')]
class RapportVetController extends AbstractController
{

    private $em;
    private $serializer;
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }




    #[Route('/', name: 'get_Rapports', methods: 'Get')]
    public function getRapports(): JsonResponse
    {
        return $this->json($this->em->getRepository(RapportVeterinaire::class)->findAll(), JsonResponse::HTTP_ACCEPTED, [], ['groups' => 'rapport:read']);
    }


    #[Route('/{id}', name: 'get_Rapport', methods: 'GET', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function getRapport(RapportVeterinaire $rapport): JsonResponse
    {
        return $this->json($rapport, JsonResponse::HTTP_OK, [], ['groups' => ['rapport:read']]);
    }



    #[Route('/add', name: 'add_Rapport', methods: 'POST')]
    public function addRapport(Request $request): JsonResponse
    {
        $rapport = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');
        $data = json_decode($request->getContent(), true);
        $idanimal = $data['animalId'];
        if (!$idanimal) {
            return $this->json('animalId is Required', JsonResponse::HTTP_BAD_REQUEST);
        }
        $rapport->setPassageDate(new DateTimeImmutable());
        $rapport->setAnimal($this->em->getRepository(Animal::class)->find($idanimal));

        $this->em->persist($rapport);
        $this->em->flush();

        return $this->json(['message' => 'rapport created'], JsonResponse::HTTP_CREATED);
    }



    #[Route('/{id}', name: 'edit_Rapport', methods: 'PUT', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function editRapport(RapportVeterinaire $rapport, Request $request): JsonResponse
    {
        $rapport = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $rapport]);
        $data = json_decode($request->getContent(), true);
        $idAnimal = $data['animalId'];
        $rapport->setAnimal($this->em->getRepository(Animal::class)->find($idAnimal));
        $rapport->setPassageDate(new DateTimeImmutable());
        $this->em->flush();


        return $this->json(['message' => 'rapport edited'], JsonResponse::HTTP_ACCEPTED);
    }




    #[Route('/{id}', name: 'delete_Rapport', methods: 'DELETE', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function deleteRapport(RapportVeterinaire $rapport): JsonResponse
    {
        $this->em->remove($rapport);
        $this->em->flush();
        return $this->json(['message' => 'rapport removed'], JsonResponse::HTTP_NO_CONTENT);
    }





    /** 
     * {
    
     * "etat": "en Bonne Santé",
     * "nourriture": "Nourriture3",
     * "autreDetail": "kalakacosnimoaslks ....",
     * "animalId": 4
    
     * }
     */
}
