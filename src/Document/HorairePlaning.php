<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection: 'HorairePlaning')]
class HorairePlaning
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field]
    #[Assert\NotBlank]
    #[Assert\Choice([
        'Lundi',
        'Mardi',
        'Mercredi',
        'Jeudi',
        'Vendredi',
        'Samedi',
        'Dimanche'
    ])]
    private string $jour;

    #[ODM\Field]
    #[Assert\NotBlank]
    private string $horaireDouverture;

    public function __construct(string $jour, string $horaireDouverture = '08h-18H')
    {
        $this->jour = $jour;
        $this->horaireDouverture = $horaireDouverture;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getJour(): string
    {
        return $this->jour;
    }

    public function setJour(string $jour): self
    {
        $this->jour = $jour;
        return $this;
    }

    public function getHoraireDouverture(): string
    {
        return $this->horaireDouverture;
    }

    public function setHoraireDouverture(string $horaireDouverture): self
    {
        $this->horaireDouverture = $horaireDouverture;
        return $this;
    }
}
