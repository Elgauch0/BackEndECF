<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/users')]
class UsersController extends AbstractController
{

    private $em;
    private $serializer;
    private $passwordHasher;
    private $validator;
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer,  ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
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


    #[Route('/add', name: 'add_user', methods: 'POST')]
    public function addUser(Request $request, MailerInterface $mailer): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $Data = json_decode($request->getContent(), true);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $Data['password']);



        $user->setRoles($Data['Vet'] ? ['ROLE_VETERINAIRE'] : ['ROLE_EMPLOYE']);
        $user->setPassword($hashedPassword);
        $errors = $this->validator->validate($user);
        if ($errors->count() > 0) {
            return new JsonResponse(['message' => 'validation failed'], JsonResponse::HTTP_BAD_REQUEST, []);
        }


        $email = (new Email())
            ->from('demo@mailtrap.com')
            ->to($user->getEmail())

            ->subject('Welcome to arcadia Zoo')
            ->html('<h1>You have been added to the database</h1> <p>Hey, welcome to our company! 
              You have been added to the site with the email you provided. Please take note of your password.</p>');

        $mailer->send($email);


        $this->em->persist($user);
        $this->em->flush();
        return $this->json(['message' => 'User Added'], JsonResponse::HTTP_CREATED);
    }
}


/***
 * {
    "email":"EMPLOYEZOO@dEMPLOYE.com",
    "prenom":" prenom Employe zoo",
    "nom":"nom Employe zoo",
    "Vet":false,
    "password":"password"
} 
 */
