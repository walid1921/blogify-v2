<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Repository\BlogsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/blog', name: 'blog.')]
final class BlogController extends AbstractController
{

    // ! Read all blogs
    #[Route('/', name: 'allBlogs', requirements: ['limit' => '\d+'])]
    public function index (BlogsRepository $br): Response
    {


        return $this->render('blog/all_blogs.html.twig', [
            'controller_name' => 'BlogController',

        ]);
    }

    // ! Update blogs
    #[Route('/update', name: 'updateBlogs', requirements: ['limit' => '\d+'])]
    public function updateBlogs (BlogsRepository $br): Response
    {
        $blogs = $br->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'blogs' => $blogs,
        ]);
    }


    // ! Create a blog
    #[Route('/create', name: 'create')]
    public function createBlog (EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog();
        $blog->setTitle('Javasript');
        $blog->setContent('Is the best programming language');

        $entityManager->persist($blog);
        $entityManager->flush();

        return new Response('Saved new blog with id ' . $blog->getId());
    }


    // ! One blog page
    #[Route('/blog/{id}', name: 'one_blog', requirements: ['id' => '\d+'])]
    public function oneBlog (int $id): Response
    {
        // Here should redirect to not found page
        if (!isset($this->blogs[$id])) {
            throw $this->createNotFoundException('Blog not found');
        }

        return $this->render('blog/oneBlog.html.twig',
            [
                'one_blog' => $this->blogs[$id],
            ]
        );
    }
}
