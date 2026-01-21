<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Deprecated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'This email address is already registered.')]
#[UniqueEntity(fields: ['googleId'], message: 'This Google account is already linked.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Please enter a valid email')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Assert\NotNull(message: "Role cannot be empty.")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
//    #[Assert\NotBlank(message: 'Password is required')]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserProfile $userProfile = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Username is required')]
    #[Assert\Length(min: 3, max: 50)]
    private ?string $username = null;

    /**
     * @var Collection<int, Likes>
     */
    // orphanRemoval: true -> is now deleting likes when user is deleted, so better to fix it and keep users likes history
    #[ORM\OneToMany(targetEntity: Likes::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $likes;

    /**
     * @var Collection<int, Blog>
     */
    #[ORM\OneToMany(targetEntity: Blog::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $blogs;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?bool $terms = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $googleId = null;

    public function __construct ()
    {
        $this->likes = new ArrayCollection();
        $this->blogs = new ArrayCollection();
    }

    public function getId (): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier (): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles (): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles (array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword (): ?string
    {
        return $this->password;
    }

    public function setPassword (string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize (): array
    {
        $data = (array)$this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[Deprecated]
    public function eraseCredentials (): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getUserProfile (): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile (UserProfile $userProfile): static
    {
        // set the owning side of the relation if necessary
        if ($userProfile->getUser() !== $this) {
            $userProfile->setUser($this);
        }

        $this->userProfile = $userProfile;

        return $this;
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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike (Likes $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Blog>
     */
    public function getBlogs (): Collection
    {
        return $this->blogs;
    }

    public function addBlog (Blog $blog): static
    {
        if (!$this->blogs->contains($blog)) {
            $this->blogs->add($blog);
            $blog->setAuthor($this);
        }

        return $this;
    }

    public function removeBlog (Blog $blog): static
    {
        if ($this->blogs->removeElement($blog)) {
            // set the owning side to null (unless already changed)
            if ($blog->getAuthor() === $this) {
                $blog->setAuthor(null);
            }
        }

        return $this;
    }

    public function isActive (): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive (bool $is_active): static
    {
        $this->is_active = $is_active;

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

    public function isTerms (): ?bool
    {
        return $this->terms;
    }

    public function setTerms (?bool $terms): static
    {
        $this->terms = $terms;

        return $this;
    }

    public function isVerified (): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified (bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getGoogleId (): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId (?string $googleId): static
    {
        $this->googleId = $googleId;
        return $this;
    }
}
