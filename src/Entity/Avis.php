<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["avis:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\Length(
        min: 2,
        max: 60,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 60 caractères."
    )]
    #[Assert\NotBlank]
    #[Groups(["avis:read"])]
    private ?string $username = null;

    #[ORM\Column(length: 500)]
    #[Assert\Length(
        min: 5,
        max: 500,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 60 caractères."
    )]
    #[Assert\NotBlank]
    #[Groups(["avis:read"])]
    private ?string $avis = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isValid = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getAvis(): ?string
    {
        return $this->avis;
    }

    public function setAvis(string $avis): static
    {
        $this->avis = $avis;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->isValid;
    }

    public function setValid(?bool $isValid): static
    {
        $this->isValid = $isValid;

        return $this;
    }
}
