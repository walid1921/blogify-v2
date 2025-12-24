<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Likes;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\BlogsRepository;
use App\Repository\LikesRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use App\Repository\UserProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BlogLikeController extends AbstractController
{
//    #[Route('/user/profile', name: 'blog_like')]
//    public function index (EntityManagerInterface $entityManager, UserProfileRepository $profileRepo, BlogsRepository $blogsRepository, UserRepository $userRepository): Response
//    {
//
//        // finding a Blog and a user
//        $blog = $blogsRepository->find(9);
//        $user = $userRepository->find(14); // You can also use $this->getUser() if you have authentication enabled
//
////        dd($blog, $user);
//
//
//        if (!$blog || !$user) {
//            throw $this->createNotFoundException('Blog or User not found');
//        }
//
//        // Check if this user has already liked this blog
//        $existingLike = $entityManager->getRepository(Likes::class)->findBy([
//            'blog' => $blog,
//            'user' => $user
//        ]);
//
//        if (!$existingLike) {
//
//            // User has not liked this blog yet
//            $like = new Likes();
//            $like->setLiked(true);
//            $like->setBlog($blog);
//            $like->setUser($user);
//
//            $entityManager->persist($like);
//            $entityManager->flush();
//
//            $message = 'Like added successfully!';
//
//
//        } else {
//
//            // Optional: Toggle like (unlike if already liked)
//            $entityManager->remove($existingLike);
//            $entityManager->flush();
//
//            $message = 'Like removed (unliked).';
//
//        }
//
//        return $this->render('user_profile/index.html.twig', [
//            'limit' => 3,
////            'likesCount' => $blog->getLikes()->count(),
//        ]);
//    }

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
