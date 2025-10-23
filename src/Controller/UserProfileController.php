<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Likes;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\BlogsRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use App\Repository\UserProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index (EntityManagerInterface $em, UserProfileRepository $profileRepo, BlogsRepository $blogsRepository, UserRepository $userRepository): Response
    {

        $user = new User();
//      $user->setEmail($this->getUser()->getEmail());
        $user->setUsername("Nils_Friesen");
        $user->setEmail("Nils_Friesend@gmail.com");
        $user->setPassword("Nils_Friesen1921");
        $user->setIsActive(true);
        $user->setCreatedAt(new DateTimeImmutable());
//
        $profile = new UserProfile();
        $profile->setUser($user);
        $profile->setCreatedAt(new DateTimeImmutable());


        $em->persist($profile);
        $em->flush();


//        // finding a Blog and a user
//        $blog = $blogsRepository->find(585);
//        $user = $userRepository->find(1); // You can also use $this->getUser() if you have authentication enabled
//
//        if (!$blog || !$user) {
//            throw $this->createNotFoundException('Blog or User not found');
//        }
//
//        // Check if this user has already liked this blog
//        $existingLike = $em->getRepository(Likes::class)->findBy([
//            'blog' => $blog,
//            'user' => $user
//        ]);
//
//        if (!$existingLike) {
//
//
//            // User has not liked this blog yet
//            $like = new Likes();
//            $like->setLiked(true);
//            $like->setBlog($blog);
//            $like->setUser($user);
//
//            $em->persist($like);
//            $em->flush();
//
//            $message = 'Like added successfully!';
//
//
//
//
//        } else {
//
//            // Optional: Toggle like (unlike if already liked)
//            $em->remove($existingLike);
//            $em->flush();
//
//            $message = 'Like removed (unliked).';
//
//        }

        return $this->render('user_profile/index.html.twig', [
            'limit' => 3,
//            'likesCount' => $blog->getLikes()->count(),
        ]);
    }
}
