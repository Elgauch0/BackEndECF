<?php

namespace App\Controller;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/reviews')]
class ReviewsController extends AbstractController
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


    #[Route('/valid', name: 'get_Reviews', methods: 'GET')]
    public function getReviewsV(): JsonResponse
    {
        return $this->json($this->em->getRepository(Avis::class)->findBy(['isValid' => true]), JsonResponse::HTTP_OK, [], ['groups' => ['avis:read']]);
    }

    #[Route('/nvalid', name: 'get_RValidation', methods: 'GET')]
    public function getReviewsN(): JsonResponse
    {
        return $this->json($this->em->getRepository(Avis::class)->findBy(['isValid' => false]), JsonResponse::HTTP_OK, [], ['groups' => ['avis:read']]);
    }



    #[Route('/add', name: 'add_Review', methods: 'Post')]
    public function addReview(Request $request): JsonResponse
    {
        $reviewDTO = $this->serializer->deserialize($request->getContent(), Avis::class, 'json');
        $errors = $this->validator->validate($reviewDTO);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $reviewDTO->setValid(false);
        $this->em->persist($reviewDTO);
        $this->em->flush();
        return $this->json(['message' => 'avis created'], JsonResponse::HTTP_CREATED);
    }


    #[Route('/{id}', name: 'manage_review', methods: ['POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function manageReview(Request $request, $id): JsonResponse
    {
        $avis = $this->em->getRepository(Avis::class)->find($id);
        if (!$avis) {
            return $this->json(['message' => 'Avis not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $action = $request->query->get('action');

        if ($action === 'validate') {
            $avis->setValid(true);
            $this->em->flush();
            return $this->json(['message' => 'Avis validated'], JsonResponse::HTTP_OK);
        } elseif ($action === 'delete') {
            $this->em->remove($avis);
            $this->em->flush();
            return $this->json(['message' => 'Avis deleted'], JsonResponse::HTTP_OK);
        }

        return $this->json(['message' => 'Invalid action'], JsonResponse::HTTP_BAD_REQUEST);
    }












    /**
     * 
     * {
     * "username": "luis Figo",
     * "avis":"ce zoo est vraiment le meilleur zoo du monde je vais jamais oublié ce jour "
     * }
     */
}
