<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'app_contact', methods: ['post'])]
    public function appContact(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $titre = $data['titre'] ?? null;
        $email = $data['email'] ?? null;
        $description = $data['description'] ?? null;

        if (!$titre || !$email || !$description) {
            return $this->json(['message' => 'Tous les champs sont obligatoires'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $titre = htmlspecialchars($titre, ENT_QUOTES, 'UTF-8');
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        $email = (new Email())
            ->from($email)
            ->to('demo@mailtrap.com')
            ->subject('Nouveau message de contact')
            ->text("Nom: $titre\nEmail: $email\nMessage: $description");

        $mailer->send($email);
        return $this->json(['message' => 'E-mail envoyé avec succès'], JsonResponse::HTTP_OK);
    }



    /**
     * {
     *
     *  "titre":"elm9wd",
     *"email":"maradno@hotmail.com",
     * "description":"ksjd skjd ks dkjs dkjs dk  jsdk skdj kjsdksjdskdsd skjd ksj d skdj sd"
     * } 
     */
}
