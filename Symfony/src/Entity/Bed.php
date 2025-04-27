<?php

namespace App\Entity;

use App\Repository\BedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BedRepository::class)]
class Bed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'beds')]
    #[Assert\NotNull(message: 'Room cannot be null.')]
    private ?Room $room = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Bed number cannot be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Bed number cannot be longer than {{ limit }} characters.'
    )]
    private ?string $bedNumber = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'bed')]
    #[Assert\All([
        new Assert\Type(type: Booking::class, message: 'Each booking must be a valid Booking object.')
    ])]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getBedNumber(): ?string
    {
        return $this->bedNumber;
    }

    public function setBedNumber(string $bedNumber): static
    {
        $this->bedNumber = $bedNumber;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setBed($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getBed() === $this) {
                $booking->setBed(null);
            }
        }

        return $this;
    }
}
