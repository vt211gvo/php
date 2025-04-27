<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The service name cannot be blank.")]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "The service price cannot be blank.")]
    #[Assert\Positive(message: "The price must be a positive number.")]
    private ?string $price = null;

    /**
     * @var Collection<int, ServiceOrder>
     */
    #[ORM\OneToMany(targetEntity: ServiceOrder::class, mappedBy: 'service')]
    private Collection $serviceOrders;

    public function __construct()
    {
        $this->serviceOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, ServiceOrder>
     */
    public function getServiceOrders(): Collection
    {
        return $this->serviceOrders;
    }

    public function addServiceOrder(ServiceOrder $serviceOrder): static
    {
        if (!$this->serviceOrders->contains($serviceOrder)) {
            $this->serviceOrders->add($serviceOrder);
            $serviceOrder->setService($this);
        }

        return $this;
    }

    public function removeServiceOrder(ServiceOrder $serviceOrder): static
    {
        if ($this->serviceOrders->removeElement($serviceOrder)) {
            // set the owning side to null (unless already changed)
            if ($serviceOrder->getService() === $this) {
                $serviceOrder->setService(null);
            }
        }

        return $this;
    }
}
