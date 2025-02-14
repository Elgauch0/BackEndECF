<?php

namespace App\Controller;

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
class HabitatController extends AbstractController
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




    #[Route('/habitat', name: 'get_Habitats', methods: 'GET')]
    public function getHabitat(): JsonResponse
    {
        return $this->json($this->em->getRepository(Habitat::class)->findAll(), JsonResponse::HTTP_OK, [], ['groups' => ['habitat:read']]);
    }


    #[Route('/habitat/{id}', name: 'get_Habitat', methods: 'GET', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function getAnimal(Habitat $habitat): JsonResponse
    {
        return $this->json($habitat, JsonResponse::HTTP_OK, [], ['groups' => ['habitat:read']]);
    }

    #[IsGranted('ROLE_EMPLOYE', message: 'No access!')]
    #[Route('/add', name: 'add_Habitat', methods: 'POST')]
    public function addHabitat(Request $request): JsonResponse
    {

        $habitatDTO = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');

        $errors = $this->validator->validate($habitatDTO);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $this->em->persist($habitatDTO);
        $this->em->flush();

        return $this->json(['message' => 'habitat created'], JsonResponse::HTTP_CREATED);
    }


    // #[Route('/{id}', name: 'edit_Habitat', methods: 'PUT', requirements: ['id' => Requirement::POSITIVE_INT])]
    // public function editHabitat(Habitat $habitat, Request $request): JsonResponse
    // {

    //     $habitatDTO = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]);
    //     $errors = $this->validator->validate($habitatDTO);
    //     if ($errors->count() > 0) {
    //         return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
    //     }
    //     $this->em->flush();

    //     return $this->json(['message' => 'habitat edited'], JsonResponse::HTTP_ACCEPTED);
    // }

    #[Route('/administration/habitat/{id}', name: 'edit_Habitat', methods: 'PUT', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function editHabitat(Habitat $habitat, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        if (array_key_exists('action', $data)) {
            $action = $data['action'];
            if ($action === 'editCommentaire') {
                if (array_key_exists('commentaire', $data)) {
                    $commentaire = $data['commentaire'];
                    if ($commentaire === null) {
                        $habitat->setCommentaire(null);
                    } else {
                        $habitat->setCommentaire($commentaire);
                    }
                    $this->em->flush();
                    return $this->json(['message' => 'commentaire modifié', 'commentaire' => $commentaire], JsonResponse::HTTP_ACCEPTED);
                } else {
                    return new JsonResponse(['message' => 'le champ commentaire est manquant'], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
            return new JsonResponse(['message' => 'action non reconnue'], JsonResponse::HTTP_BAD_REQUEST);
        } else {
            $habitatDTO = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]);

            $errors = $this->validator->validate($habitatDTO);
            if ($errors->count() > 0) {
                return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
            }

            $this->em->flush();

            return $this->json(['message' => 'habitat modifié'], JsonResponse::HTTP_ACCEPTED);
        }
    }



    #[Route('/administration/habitat{id}', name: 'delete_Habitat', methods: 'DELETE', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function deleteHabitat(Habitat $habitat): JsonResponse
    {
        $this->em->remove($habitat);
        $this->em->flush();
        return $this->json(['message' => 'habitat removed'], JsonResponse::HTTP_NO_CONTENT);
    }




    /**
     * {
     * 'nom':'habitat nom',
     * 'description:'habitat description
     * }
     * 
     * 
     */
}
