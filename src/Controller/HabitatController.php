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
use Symfony\Component\HttpFoundation\File\UploadedFile;


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
    public function getHabitats(): JsonResponse
    {
        return $this->json($this->em->getRepository(Habitat::class)->findAll(), JsonResponse::HTTP_OK, [], ['groups' => ['habitat:read']]);
    }


    #[Route('/habitat/{id}', name: 'get_Habitat', methods: 'GET', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function getHabitat(Habitat $habitat): JsonResponse
    {
        return $this->json($habitat, JsonResponse::HTTP_OK, [], ['groups' => ['habitat:read']]);
    }




    #[Route('/administration/habitat/add', name: 'add_Habitat', methods: 'POST')]
    public function addHabitat(Request $request): JsonResponse

    {
        $habitat = new Habitat();


        $nom = $request->request->get('nom');
        $description = $request->request->get('description');
        if (!$nom || !$description) {
            return new JsonResponse(['message' => 'Données manquantes'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $habitat->setNom($nom);
        $habitat->setDescription($description);

        // Gérer le fichier image
        /** @var UploadedFile $imageFile */
        $imageFile = $request->files->get('imageFile');
        if ($imageFile) {
            $habitat->setImageFile($imageFile);
        } else {
            return new JsonResponse(['message' => 'Image requise'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validation
        $errors = $this->validator->validate($habitat);
        if ($errors->count() > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['message' => 'Validation échouée', 'errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Enregistrement
        $this->em->persist($habitat);
        $this->em->flush();

        return $this->json(['message' => 'Habitat créé'], JsonResponse::HTTP_CREATED);
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



    #[Route('/administration/habitat/{id}', name: 'edit_Habitat', methods: ['POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function editHabitat(Habitat $habitat, Request $request): JsonResponse
    {
        $nom = $request->request->get('nom');
        $description = $request->request->get('description');


        if (!$nom || !$description) {
            return new JsonResponse(['message' => 'Données manquantes'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $habitat->setNom($nom);
        $habitat->setDescription($description);

        // Gérer le fichier image (facultatif)
        /** @var UploadedFile $imageFile */
        $imageFile = $request->files->get('imageFile');
        if ($imageFile) {
            $habitat->setImageFile($imageFile);
        }

        // Validation
        $errors = $this->validator->validate($habitat);
        if ($errors->count() > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['message' => 'Validation échouée', 'errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Enregistrement des modifications
        $this->em->flush();

        return $this->json(['message' => 'Habitat modifié'], JsonResponse::HTTP_OK);
    }

    #[Route('/administration/habitat/avis/{id}', name: 'editAvis_Habitat', methods: ['Put'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function editAvis(Habitat $habitat, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['action'])) {
            if ($data['action'] === 'editCommentaire') {
                $habitat->setCommentaire($data['commentaire']);
                $this->em->flush();
                return $this->json(['message' => 'commentaire modifié'], JsonResponse::HTTP_OK);
            }
        }

        return new JsonResponse(['message' => 'Validation échouée', 'errors' => 'missing data'], JsonResponse::HTTP_BAD_REQUEST);
    }


    #[Route('/administration/habitat/{id}', name: 'delete_Habitat', methods: 'DELETE', requirements: ['id' => Requirement::POSITIVE_INT])]
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
