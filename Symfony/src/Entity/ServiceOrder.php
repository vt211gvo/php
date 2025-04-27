<?php

namespace App\Entity;

use App\Repository\ServiceOrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceOrderRepository::class)]
class ServiceOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'serviceOrders')]
    #[Assert\NotNull(message: 'Guest cannot be null')]
    #[Assert\Valid]
    private ?Guest $guest = null;

    #[ORM\ManyToOne(inversedBy: 'serviceOrders')]
    #[Assert\NotNull(message: 'Service cannot be null')]
    #[Assert\Valid]
    private ?Service $service = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'Order date cannot be null')]
    #[Assert\Date(message: 'Order date must be a valid date')]
    #[Assert\LessThanOrEqual("today", message: 'Order date must be today or in the past')]
    private ?\DateTimeInterface $orderDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuest(): ?Guest
    {
        return $this->guest;
    }

    public function setGuest(?Guest $guest): static
    {
        $this->guest = $guest;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }
}
