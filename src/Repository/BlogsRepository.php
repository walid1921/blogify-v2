<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 */
class BlogsRepository extends ServiceEntityRepository
{
    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    public function findMostRecent (?string $category = null): array
    {
        return $this->basePublishedQuery($category)
            ->orderBy('b.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    private function basePublishedQuery (?string $category = null)
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.categories', 'c')
            ->andWhere('b.is_published = true');

        if ($category) {
            $qb
                ->andWhere('c.name = :name')
                ->setParameter('name', $category);
        }

        return $qb;
    }

    public function findMostLiked (?string $category = null): array
    {
        return $this->basePublishedQuery($category)
            ->leftJoin('b.likes', 'l')
            ->groupBy('b.id')
            ->orderBy('COUNT(l.id)', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findMostRead (?string $category = null): array
    {
        return $this->basePublishedQuery($category)
            ->orderBy('b.read_time', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLikedByCurrentUser (User $user, ?string $category = null): array
    {
        return $this->basePublishedQuery($category)
            ->innerJoin('b.likes', 'l')
            ->andWhere('l.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByDate (string $order = 'DESC'): array
    {
        return $this->createQueryBuilder('b')
            ->addSelect('a', 'c') // select joined entities
            ->leftJoin('b.author', 'a')
            ->leftJoin('b.categories', 'c')
            ->orderBy('b.created_at', $order)
            ->getQuery()
            ->getResult();

    }

//    public function findLikedByCurrentUser (User $user): array
//    {
//        return $this->createQueryBuilder('b')
//            ->addSelect('l', 'c', 'a') // Tells Doctrine to fetch these joined objects in the same query (avoid extra DB hits).
//            ->innerJoin('b.likes', 'l') // INNER JOIN ensures we only get blogs that actually have likes.
//            ->leftJoin('b.categories', 'c') // categories & author -> This is performance optimization: Without it, Twig loops might trigger extra queries (Doctrine lazy loading), especially when you print categories inside the loop.
//            ->leftJoin('b.author', 'a')
//            ->andWhere('l.user = :user') // only blogs liked by this user.
//            ->setParameter('user', $user)
//            ->orderBy('b.created_at', 'DESC') // sorting favorites by most recent blog creation date.
//            ->getQuery()
//            ->getResult();
//    }

    public function findLatestPublished (?int $limit = 3): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.is_published = :published')
            ->setParameter('published', true)
            ->orderBy('b.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findHighlitedBlogs (int $limit = 3): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.is_published = :published')
            ->setParameter('published', true)
            ->orderBy('b.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByAuthorSortedByDate (User $author, string $order = 'DESC'): array
    {
        return $this->createQueryBuilder('b')
            ->addSelect('a', 'c')
            ->leftJoin('b.author', 'a')
            ->leftJoin('b.categories', 'c')
            ->andWhere('b.author = :author')
            ->setParameter('author', $author)
            ->orderBy('b.created_at', $order)
            ->getQuery()
            ->getResult();
    }


    public function findPaginated (
        int     $page,
        int     $limit,
        string  $sort,
        ?string $category,
        ?User   $user = null
    ): Paginator
    {
        $offset = ($page - 1) * $limit;

        $qb = match ($sort) {
            'most_liked' => $this->basePublishedQuery($category)
                ->leftJoin('b.likes', 'l')
                ->groupBy('b.id')
                ->orderBy('COUNT(l.id)', 'DESC'),

            'most_read' => $this->basePublishedQuery($category)
                ->orderBy('b.read_time', 'DESC'),

            'i_liked' => $this->basePublishedQuery($category)
                ->innerJoin('b.likes', 'l')
                ->andWhere('l.user = :user')
                ->setParameter('user', $user)
                ->orderBy('b.created_at', 'DESC'),

            default => $this->basePublishedQuery($category)
                ->orderBy('b.created_at', 'DESC'),
        };

        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($qb);
    }


}
