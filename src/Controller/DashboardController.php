<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\BlogCategoriesRepository;
use App\Repository\BlogsRepository;
use App\Repository\LikesRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard', name: 'dashboard.')]
final class DashboardController extends AbstractController
{
    // ! All Blogs in a table
    #[Route('/blogs', name: 'allBlogs')]
    public function allBlogs (Request $request, BlogsRepository $blogsRepository, LikesRepository $likesRepository): Response
    {
        $order = $request->query->get('order', 'DESC'); // default DESC
        $blogs = $blogsRepository->findAllSortedByDate($order); // Retrieve all blogs sorted by creation date

        $blogIds = array_map(fn ($blog) => $blog->getId(), $blogs); // Collect blog IDs

        $likesCount = $likesRepository->countLikesForBlogs($blogIds); // Fetch like counts for all blogs at once

        return $this->render('dashboard/index.html.twig', [
            'blogs' => $blogs,
            'order' => $order,
            'likesCount' => $likesCount,
        ]);
    }

    // ! All Blog Categories
    #[Route('/categories', name: 'allBlogCategories')]
    public function allBlogCategories (BlogCategoriesRepository $categoriesRepo): Response
    {
        $blogCategories = $categoriesRepo->findAll();

        return $this->render('dashboard/index.html.twig', [
            'blogCategories' => $blogCategories,
        ]);
    }

    // ! All Users
    #[Route('/users', name: 'users')]
    public function users (UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('dashboard/index.html.twig', [
            'users' => $users,
        ]);
    }

    // ! User Guide
    #[Route('/guide', name: 'userGuide')]
    public function userGuide (): Response
    {
        $message = "Hi there";

        return $this->render('dashboard/index.html.twig', [
            'message' => $message
        ]);
    }
}
