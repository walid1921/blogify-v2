<?php

namespace App\Entity;

use App\Repository\BlogCategoriesRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: ['name'], message: 'This category already exists.')]
#[ORM\Entity(repositoryClass: BlogCategoriesRepository::class)]
class BlogCategories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(min: 2, max: 50)]
    #[Assert\NotNull(message: "Name cannot be empty.")]
    private ?string $name = null;

    #[ORM\Column]
    private ?DateTime $created_at = null;

    /**
     * @var Collection<int, Blog>
     */
    #[ORM\ManyToMany(targetEntity: Blog::class, mappedBy: 'categories')]
    private Collection $blogs;

    public function __construct()
    {
        $this->blogs = new ArrayCollection();
    }

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getName (): ?string
    {
        return $this->name;
    }

    public function setName (string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt (): ?DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt (DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Blog>
     */
    public function getBlogs(): Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): static
    {
        if (!$this->blogs->contains($blog)) {
            $this->blogs->add($blog);
            $blog->addCategory($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): static
    {
        if ($this->blogs->removeElement($blog)) {
            $blog->removeCategory($this);
        }

        return $this;
    }
}
