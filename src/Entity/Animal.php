<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["animals:read", "habitat:read", "rapport:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups(["animals:read"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["animals:read"])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'animaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitat $habitat = null;

    /**
     * @var Collection<int, RapportVeterinaire>
     */
    #[ORM\OneToMany(targetEntity: RapportVeterinaire::class, mappedBy: 'animal', orphanRemoval: true)]
    private Collection $rapport_vet;

    public function __construct()
    {
        $this->rapport_vet = new ArrayCollection();
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

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    /**
     * @return Collection<int, RapportVeterinaire>
     */
    public function getRapportVet(): Collection
    {
        return $this->rapport_vet;
    }

    public function addRapportVet(RapportVeterinaire $rapportVet): static
    {
        if (!$this->rapport_vet->contains($rapportVet)) {
            $this->rapport_vet->add($rapportVet);
            $rapportVet->setAnimal($this);
        }

        return $this;
    }

    public function removeRapportVet(RapportVeterinaire $rapportVet): static
    {
        if ($this->rapport_vet->removeElement($rapportVet)) {
            // set the owning side to null (unless already changed)
            if ($rapportVet->getAnimal() === $this) {
                $rapportVet->setAnimal(null);
            }
        }

        return $this;
    }
}
