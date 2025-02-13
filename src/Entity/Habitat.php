<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HabitatRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HabitatRepository::class)]
class Habitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["habitat:read", "animals:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups(["habitat:read", "animals:read"])]
    #[Assert\Length(
        min: 5,
        max: 60,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 60 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["habitat:read"])]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 255 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $description = null;

    /**
     * @var Collection<int, animal>
     */
    #[ORM\OneToMany(targetEntity: animal::class, mappedBy: 'habitat', orphanRemoval: true)]
    #[Groups(["habitat:read"])]

    private Collection $animaux;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["habitat:read"])]
    private ?string $commentaire = null;

    public function __construct()
    {
        $this->animaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, animal>
     */
    public function getAnimaux(): Collection
    {
        return $this->animaux;
    }

    public function addAnimaux(animal $animaux): static
    {
        if (!$this->animaux->contains($animaux)) {
            $this->animaux->add($animaux);
            $animaux->setHabitat($this);
        }

        return $this;
    }

    public function removeAnimaux(animal $animaux): static
    {
        if ($this->animaux->removeElement($animaux)) {
            // set the owning side to null (unless already changed)
            if ($animaux->getHabitat() === $this) {
                $animaux->setHabitat(null);
            }
        }

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
