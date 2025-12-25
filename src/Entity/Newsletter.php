<?php

namespace App\Entity;

use App\Repository\NewsletterRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'This email address is already registered, please try another one.')]
class Newsletter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Please enter a valid email')]
    private ?string $email = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getUsername (): ?string
    {
        return $this->username;
    }

    public function setUsername (string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail (): ?string
    {
        return $this->email;
    }

    public function setEmail (string $email): static
    {
        $this->email = $email;

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
}
