<?php

namespace App\Entity;

use App\Repository\AlimentationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AlimentationRepository::class)]
class Alimentation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'alimentations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Animal $animal_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 255 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $nourritureDonnée = null;

    #[ORM\Column(length: 60)]
    #[Assert\Length(
        min: 2,
        max: 60,
        minMessage: "Le champ doit contenir au moins 2 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 60 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $quantité = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $givenAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnimalId(): ?Animal
    {
        return $this->animal_id;
    }

    public function setAnimalId(?Animal $animal_id): static
    {
        $this->animal_id = $animal_id;

        return $this;
    }

    public function getNourritureDonnée(): ?string
    {
        return $this->nourritureDonnée;
    }

    public function setNourritureDonnée(string $nourritureDonnée): static
    {
        $this->nourritureDonnée = $nourritureDonnée;

        return $this;
    }

    public function getQuantité(): ?string
    {
        return $this->quantité;
    }

    public function setQuantité(string $quantité): static
    {
        $this->quantité = $quantité;

        return $this;
    }

    public function getGivenAt(): ?\DateTimeImmutable
    {
        return $this->givenAt;
    }

    public function setGivenAt(\DateTimeImmutable $givenAt): static
    {
        $this->givenAt = $givenAt;

        return $this;
    }
}
