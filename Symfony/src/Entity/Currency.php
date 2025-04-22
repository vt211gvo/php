<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $asset = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsset(): ?string
    {
        return $this->asset;
    }

    public function setAsset(string $asset): static
    {
        $this->asset = $asset;

        return $this;
    }
}
