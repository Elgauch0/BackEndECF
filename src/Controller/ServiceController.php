<?php

namespace App\Controller;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ServiceController extends AbstractController
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



    #[Route('/services', name: 'get_Services', methods: 'GET')]
    public function getServices(): JsonResponse
    {
        return $this->json($this->em->getRepository(Service::class)->findAll(), JsonResponse::HTTP_OK);
    }




    #[Route('/administration/services/{id}', name: 'edit_Service', methods: 'PUT', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function editService(Service $service, Request $request): JsonResponse
    {
        $serviceDTO = $this->serializer->deserialize($request->getContent(), Service::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $service]);
        $errors = $this->validator->validate($serviceDTO);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }

        $this->em->flush();
        return $this->json(['message' => 'Service edited'], JsonResponse::HTTP_ACCEPTED);
    }






    #[Route('/administration/services/add', name: 'add_Service', methods: 'POST')]
    public function addService(Request $request): JsonResponse
    {
        $serviceDTO = $this->serializer->deserialize($request->getContent(), Service::class, 'json');
        $errors = $this->validator->validate($serviceDTO);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $this->em->persist($serviceDTO);
        $this->em->flush();
        return $this->json(['message' => 'service created'], JsonResponse::HTTP_CREATED);
    }





    #[Route('/administration/services/{id}', name: 'delete_Service', methods: "DELETE", requirements: ['id' => Requirement::POSITIVE_INT])]
    public function deleteService(Service $service): JsonResponse
    {
        $this->em->remove($service);
        $this->em->flush();
        return $this->json(['message' => 'service removed'], JsonResponse::HTTP_NO_CONTENT);
    }
}
