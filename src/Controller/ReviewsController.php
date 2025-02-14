<?php

namespace App\Controller;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api')]
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


    #[Route('/reviews/valid', name: 'get_Reviews', methods: 'GET')]
    public function getReviewsV(): JsonResponse
    {
        return $this->json($this->em->getRepository(Avis::class)->findBy(['isValid' => true]), JsonResponse::HTTP_OK, [], ['groups' => ['avis:read']]);
    }


    #[Route('/administration/reviews/nvalid', name: 'get_RValidation', methods: 'GET')]
    public function getReviewsN(): JsonResponse
    {
        return $this->json($this->em->getRepository(Avis::class)->findBy(['isValid' => false]), JsonResponse::HTTP_OK, [], ['groups' => ['avis:read']]);
    }



    #[Route('/administration/reviews/add', name: 'add_Review', methods: 'Post')]
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


    #[Route('/administration/reviews/{id}', name: 'manage_review', methods: ['POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function manageReview(Avis $avis, Request $request): JsonResponse
    {

        if (!$avis) {
            throw new NotFoundHttpException('Avis non trouvé');
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
