<?php

namespace App\Entity;

use App\Repository\BlogsRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogsRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "The title must be at least {{ limit }} characters long.",
        maxMessage: "The title cannot be longer than {{ limit }} characters."
    )]
    #[Assert\NotNull(message: "Title cannot be empty.")]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "You must specify a creation date.")]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column]
//    #[Assert\Type('bool', message: "Published must be true or false.")]
    private ?bool $is_published = null;

//    #[ORM\Column]
//    #[Assert\NotNull(message: "Likes cannot be null.")]
//    #[Assert\PositiveOrZero(message: "Likes must be zero or a positive number.")]
//    private ?int $likes = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverImage = null;

    #[ORM\Column(nullable: true)]
    private ?int $read_time = null;

    #[ORM\Column(length: 255)]
    private ?string $blog_language = null;

    /**
     * @var Collection<int, Likes>
     */
    #[ORM\OneToMany(targetEntity: Likes::class, mappedBy: 'blog', cascade: ['persist'], orphanRemoval: true)]
    private Collection $likes;

    #[ORM\ManyToOne(inversedBy: 'blogs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $author = null;

    public function __construct ()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getTitle (): ?string
    {
        return $this->title;
    }

    public function setTitle (?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent (): ?string
    {
        return $this->content;
    }

    public function setContent (?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt (): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt (?DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isPublished (): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished (bool $is_published): static
    {
        $this->is_published = $is_published;

        return $this;
    }

//    public function getLikes (): ?int
//    {
//        return $this->likes;
//    }
//
//    public function setLikes (int $likes): static
//    {
//        $this->likes = $likes;
//
//        return $this;
//    }

    public function getCoverImage (): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage (?string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getReadTime (): ?int
    {
        return $this->read_time;
    }

    public function setReadTime (?int $read_time = null): static
    {
        $this->read_time = $read_time;

        return $this;
    }

    public function getBlogLanguage (): ?string
    {
        return $this->blog_language;
    }

    public function setBlogLanguage (string $blog_language): static
    {
        $this->blog_language = $blog_language;

        return $this;
    }

    /**
     * @return Collection<int, Likes>
     */
    public function getLikes (): Collection
    {
        return $this->likes;
    }

    public function addLike (Likes $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setBlog($this);
        }

        return $this;
    }

    public function removeLike (Likes $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getBlog() === $this) {
                $like->setBlog(null);
            }
        }

        return $this;
    }

    public function getAuthor (): ?User
    {
        return $this->author;
    }

    public function setAuthor (?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
