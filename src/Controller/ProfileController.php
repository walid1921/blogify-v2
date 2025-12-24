<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BlogsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/blogs/{id}', name: 'user_profile')]
    public function show (User $user, BlogsRepository $blogRepo): Response
    {

        //! Fetch latest published blogs
        $latestBlogs = $blogRepo->findLatestPublished();

        foreach ($latestBlogs as $blog) {
            $blog->getExcerpt();
        }


        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'blogs' => $user->getBlogs(),
            'latestBlogs' => $latestBlogs,
        ]);
    }

    #[Route('/profile/about/{id}', name: 'user_about')]
    public function aboutUser (User $user, BlogsRepository $blogRepo): Response
    {
        //! Fetch latest published blogs
        $latestBlogs = $blogRepo->findLatestPublished();

        foreach ($latestBlogs as $blog) {
            $blog->getExcerpt();
        }

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'latestBlogs' => $latestBlogs
        ]);
    }
}
