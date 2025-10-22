<?php

namespace App\Entity;

use App\Repository\LikesRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikesRepository::class)]
class Likes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $liked = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Blog $blog = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct ()
    {
        // Automatically set creation date when a Like is created
        $this->created_at = new DateTimeImmutable();
    }


    public function getId (): ?int
    {
        return $this->id;
    }

    public function isLiked (): ?bool
    {
        return $this->liked;
    }

    public function setLiked (bool $liked): static
    {
        $this->liked = $liked;

        return $this;
    }

    public function getBlog (): ?Blog
    {
        return $this->blog;
    }

    public function setBlog (?Blog $blog): static
    {
        $this->blog = $blog;

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

    public function getUser (): ?User
    {
        return $this->user;
    }

    public function setUser (?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
