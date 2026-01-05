<?php

namespace App\Entity;

use App\Repository\UserProfileRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $dateOfBirth = null;

    #[ORM\OneToOne(inversedBy: 'userProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover_image = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagram = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tiktok = null;

    public function getId (): ?int
    {
        return $this->id;
    }


    public function getBio (): ?string
    {
        return $this->bio;
    }

    public function setBio (?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getDateOfBirth (): ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth (?DateTime $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getUser (): ?User
    {
        return $this->user;
    }

    public function setUser (User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAvatar (): ?string
    {
        return $this->avatar;
    }

    public function setAvatar (?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCoverImage (): ?string
    {
        return $this->cover_image;
    }

    public function setCoverImage (?string $cover_image): static
    {
        $this->cover_image = $cover_image;

        return $this;
    }

    public function getCreatedAt (): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt (DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCountry (): ?string
    {
        return $this->country;
    }

    public function setCountry (?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getInstagram (): ?string
    {
        return $this->instagram;
    }

    public function setInstagram (?string $instagram): static
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getTiktok (): ?string
    {
        return $this->tiktok;
    }

    public function setTiktok (?string $tiktok): static
    {
        $this->tiktok = $tiktok;

        return $this;
    }
}
