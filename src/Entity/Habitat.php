<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\HabitatRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: HabitatRepository::class)]
class Habitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["habitat:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups(["habitat:read"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["habitat:read"])]
    private ?string $description = null;

    /**
     * @var Collection<int, animal>
     */
    #[ORM\OneToMany(targetEntity: animal::class, mappedBy: 'habitat', orphanRemoval: true)]

    private Collection $animaux;

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
}
