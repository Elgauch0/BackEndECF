<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'AnimalStatistic')]
class AnimalCount
{
    #[ODM\Id]
    public ?string $id = null;

    #[ODM\Field]
    public string $animalName;

    #[ODM\Field]
    public int $animalCountVisit;

    public function __construct(string $animalName, int $animalCountVisit = 0)
    {
        $this->animalName = $animalName;
        $this->animalCountVisit = $animalCountVisit;
    }

    public function getId(): ?string
    {
        return $this->id;
    }


    public function getAnimalName(): string
    {
        return $this->animalName;
    }
    public function setAnimalName(string $name): self
    {
        $this->animalName = $name;
        return $this;
    }

    public function getAnimalCountVisit(): int
    {
        return $this->animalCountVisit;
    }

    public function setAnimalCountVisit(int $animalCountVisit): self
    {
        $this->animalCountVisit = $animalCountVisit;

        return $this;
    }
}
