<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Entity\Likes;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Likes>
 */
class LikesRepository extends ServiceEntityRepository
{
    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    //    /**
    //     * @return Likes[] Returns an array of Likes objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Likes
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function countLikesForBlogs (array $blogIds): array
    {
        if (empty($blogIds)) {
            return [];
        }

        $qb = $this->createQueryBuilder('l')
            ->select('IDENTITY(l.blog) AS blog_id, COUNT(l.id) AS like_count')
            ->where('l.blog IN (:ids)')
            ->setParameter('ids', $blogIds)
            ->groupBy('l.blog');

        $results = $qb->getQuery()->getResult();

        // Convert to [blog_id => like_count] format for easy lookup
        $counts = [];
        foreach ($results as $row) {
            $counts[$row['blog_id']] = $row['like_count'];
        }

        return $counts;
    }

    public function findOneByBlogAndUser (Blog $blog, User $user): ?Likes
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.blog = :blog')
            ->andWhere('l.user = :user')
            ->setParameter('blog', $blog)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
