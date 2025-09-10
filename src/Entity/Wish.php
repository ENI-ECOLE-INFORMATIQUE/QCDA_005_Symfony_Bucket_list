<?php

namespace App\Entity;

use App\Repository\WishRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishRepository::class)]
class Wish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\NotBlank(message: 'Please provide an idea !')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Minimum {{ limit }} characters please !',
        maxMessage: 'Maximum {{ limit }} characters please !')]
    #[ORM\Column(length: 250)]
    private ?string $title = null;


    #[Assert\Length(
        min: 5,
        max: 5000,
        minMessage: 'Minimum {{ limit }} characters please !',
        maxMessage: 'Maximum {{ limit }} characters please !')]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank(message: 'Please provide your username !')]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Minimum {{ limit }} characters please !',
        maxMessage: 'Maximum {{ limit }} characters please !')]
    #[Assert\Regex(pattern: "/^[a-z0-9_-]+$/i",
        message: 'Please use only alphanumeric characters, dashes and underscores.', )]
    #[ORM\Column(length: 50)]
    private ?string $author = null;

    #[ORM\Column]
    private ?bool $published = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateUpdated = null;
    public function __construct()
    {
        $this->dateCreated = new \DateTimeImmutable();
        $this->published = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeImmutable
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeImmutable $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeImmutable
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?\DateTimeImmutable $dateUpdated): static
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }
}
