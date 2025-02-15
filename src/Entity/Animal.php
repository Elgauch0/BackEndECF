<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
#[Vich\Uploadable]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["animals:read", "habitat:read", "rapport:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups(["animals:read", "alimentation:read", "rapport:read", "habitat:read"])]
    #[Assert\Length(
        min: 5,
        max: 60,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 60 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["animals:read"])]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 255 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'animaux')]
    #[Groups(["animals:read"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitat $habitat = null;

    /**
     * @var Collection<int, RapportVeterinaire>
     */
    #[ORM\OneToMany(targetEntity: RapportVeterinaire::class, mappedBy: 'animal', orphanRemoval: true)]
    private Collection $rapport_vet;

    /**
     * @var Collection<int, Alimentation>
     */
    #[ORM\OneToMany(targetEntity: Alimentation::class, mappedBy: 'animal_id', cascade: ['remove'])]
    private Collection $alimentations;



    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'animal_image', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["animals:read"])]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;


    public function __construct()
    {
        $this->rapport_vet = new ArrayCollection();
        $this->alimentations = new ArrayCollection();
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

    /**
     * @return Collection<int, Alimentation>
     */
    public function getAlimentations(): Collection
    {
        return $this->alimentations;
    }

    public function addAlimentation(Alimentation $alimentation): static
    {
        if (!$this->alimentations->contains($alimentation)) {
            $this->alimentations->add($alimentation);
            $alimentation->setAnimalId($this);
        }

        return $this;
    }

    public function removeAlimentation(Alimentation $alimentation): static
    {
        if ($this->alimentations->removeElement($alimentation)) {
            // set the owning side to null (unless already changed)
            if ($alimentation->getAnimalId() === $this) {
                $alimentation->setAnimalId(null);
            }
        }

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }
}
