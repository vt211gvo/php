<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[Assert\NotNull(message: 'Guest is required.')]
    private ?Guest $guest = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Comment cannot be blank.')]
    #[Assert\Length(min: 10, max: 1000, minMessage: 'Comment must be at least {{ limit }} characters long.', maxMessage: 'Comment cannot exceed {{ limit }} characters.')]
    private ?string $comment = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Rating is required.')]
    #[Assert\Range(notInRangeMessage: 'Rating must be between {{ min }} and {{ max }}.', min: 1, max: 5)]
    private ?int $rating = null;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }
}
