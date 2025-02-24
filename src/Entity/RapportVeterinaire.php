<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RapportVeterinaireRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RapportVeterinaireRepository::class)]
class RapportVeterinaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["rapport:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups(["rapport:read"])]
    #[Assert\Length(
        min: 5,
        max: 60,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 60 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $etat = null;

    #[ORM\Column(length: 100)]
    #[Groups(["rapport:read"])]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 100 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $nourriture = null;

    #[ORM\Column]
    #[Groups(["rapport:read"])]
    private ?\DateTimeImmutable $passage_Date = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 255 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $autreDetail = null;

    #[ORM\ManyToOne(inversedBy: 'rapport_vet')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["rapport:read"])]
    private ?Animal $animal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getNourriture(): ?string
    {
        return $this->nourriture;
    }

    public function setNourriture(string $nourriture): static
    {
        $this->nourriture = $nourriture;

        return $this;
    }

    public function getPassageDate(): ?\DateTimeImmutable
    {
        return $this->passage_Date;
    }

    public function setPassageDate(\DateTimeImmutable $passage_Date): static
    {
        $this->passage_Date = $passage_Date;

        return $this;
    }

    public function getAutreDetail(): ?string
    {
        return $this->autreDetail;
    }

    public function setAutreDetail(?string $autreDetail): static
    {
        $this->autreDetail = $autreDetail;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }
}
