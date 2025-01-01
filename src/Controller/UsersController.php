<?php

namespace App\Controller;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/users')]
class UsersController extends AbstractController
{

    private $em;
    private $serializer;
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }



    #[Route('/', name: 'get_Users', methods: ['Get'])]
    public function getUsers(): JsonResponse
    {
        return $this->json($this->em->getRepository(User::class)->findAll(), JsonResponse::HTTP_OK, [], ['groups' => ['users:read']]);
    }

    #[Route('/{id}', name: 'delete_User', methods: 'DELETE', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function deleteUser(User $user): JsonResponse
    {
        if (in_array('Admin_Role', $user->getRoles())) {
            return $this->json(['message' => 'you can not delete an admin account'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $this->em->remove($user);
        $this->em->flush();
        return $this->json(['message' => 'user deleted'], JsonResponse::HTTP_NO_CONTENT);
    }
}
