<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    //    /**
    //     * @return Blogs[] Returns an array of Blogs objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Blogs
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllSortedByDate (string $order = 'DESC'): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.created_at', $order) // ASC or DESC
            ->getQuery()
            ->getResult();
    }

    public function findAllPublished (): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.is_published = :published')
            ->setParameter('published', true)
            ->orderBy('b.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestPublished (int $limit = 3): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.is_published = :published')
            ->setParameter('published', true)
            ->orderBy('b.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

//    public function findRandomBlogs (int $limit = 3): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.is_published = :published')
//            ->setParameter('published', true)
//            ->orderBy('b.created_at', 'DESC')
//            ->setMaxResults($limit)
//            ->getQuery()
//            ->getResult();
//    }

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


}
