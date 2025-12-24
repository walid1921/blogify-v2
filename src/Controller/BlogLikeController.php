<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Likes;
use App\Entity\User;
use App\Repository\LikesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BlogLikeController extends AbstractController
{
    #[Route('/blog/{id}/like', name: 'blog_like', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function toggleLike (
        Request                $request,
        Blog                   $blog,
        LikesRepository        $likesRepository,
        EntityManagerInterface $em
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $existingLike = $likesRepository->findOneByBlogAndUser($blog, $user);

        if ($existingLike) {
            // UNLIKE
            $em->remove($existingLike);
        } else {
            // LIKE
            $like = new Likes();
            $like->setBlog($blog);
            $like->setUser($user);
            $like->setLiked(true);

            $em->persist($like);
        }

        $em->flush();

        return $this->redirect(
            $request->headers->get('referer') ?? '/'
        );
    }
}
