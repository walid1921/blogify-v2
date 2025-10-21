<?php

namespace App\Controller;

use App\Repository\BlogCategoriesRepository;
use App\Repository\BlogsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard', name: 'dashboard.')]
final class DashboardController extends AbstractController
{
    // ! All Blogs in a table
    #[Route('/blogs', name: 'allBlogs')]
    public function allBlogs (Request $request, BlogsRepository $blogsRepository): Response
    {
        $order = $request->query->get('order', 'DESC'); // default DESC
        $blogs = $blogsRepository->findAllSortedByDate($order);

        return $this->render('dashboard/index.html.twig', [
            'blogs' => $blogs,
            'order' => $order,
        ]);
    }

    // ! All Blog Categories
    #[Route('/categories', name: 'allBlogCategories')]
    public function allBlogCategories (BlogCategoriesRepository $categoriesRepository): Response
    {
        $blogCategories = $categoriesRepository->findAll();

        return $this->render('dashboard/index.html.twig', [
            'blogCategories' => $blogCategories,
        ]);
    }

    // ! All Users
    #[Route('/users', name: 'users')]
    public function users (UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('dashboard/index.html.twig', [
            'users' => $users,
        ]);
    }
}
