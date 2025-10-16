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

    public function findRandomBlogs (int $limit = 3): array
    {
        // Get total count of blogs
        $total = $this->count([]);

        if ($total === 0) {
            return [];
        }

        // Pick random offsets (make sure we don't exceed total)
        $offsets = [];
        while (count($offsets) < $limit && count($offsets) < $total) {
            $offset = random_int(0, $total - 1);
            if (!in_array($offset, $offsets, true)) {
                $offsets[] = $offset;
            }
        }

        // Fetch each blog by offset (Doctrine QueryBuilder supports setFirstResult + setMaxResults)
        $randomBlogs = [];
        foreach ($offsets as $offset) {
            $blog = $this->createQueryBuilder('b')
                ->setFirstResult($offset)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($blog) {
                $randomBlogs[] = $blog;
            }
        }

        return $randomBlogs;
    }


}
