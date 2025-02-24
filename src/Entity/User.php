<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["users:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["users:read"])]
    #[Assert\Email(message: "L'adresse e-mail n'est pas valide.")]
    #[Assert\NotBlank]
    private ?string $email = null;

    /** 
     * @var list<string> The user roles 
     */
    #[ORM\Column]
    #[Groups(["users:read"])]
    #[Assert\NotBlank]
    private array $roles = [];

    /** 
     * @var string The hashed password 
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide.")]
    #[Assert\Length(
        min: 8,
        max: 64,
        minMessage: "Le mot de passe doit contenir au moins 8 caractères.",
        maxMessage: "Le mot de passe ne peut pas dépasser 64 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Groups(["users:read"])]
    #[Assert\Length(
        min: 5,
        max: 60,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 60 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    #[Groups(["users:read"])]
    #[Assert\Length(
        min: 5,
        max: 60,
        minMessage: "Le champ doit contenir au moins 5 caractères.",
        maxMessage: "Le champ ne peut pas dépasser 60 caractères."
    )]
    #[Assert\NotBlank]
    private ?string $nom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /** 
     * A visual identifier that represents this user. 
     * 
     * @see UserInterface 
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /** 
     * @see UserInterface 
     * 
     * @return list<string> 
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_EMPLOYE';
        return array_unique($roles);
    }

    /** 
     * @param list<string> $roles 
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /** 
     * @see PasswordAuthenticatedUserInterface 
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /** 
     * @see UserInterface 
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
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
}
