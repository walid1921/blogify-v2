<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogCategoriesRepository;
use App\Repository\BlogsRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/blog', name: 'blog.')]
final class BlogController extends AbstractController
{

    // ! Fetch All Blogs
    #[Route('/', name: 'allBlogs', requirements: ['limit' => '\d+'])]
    public function index (BlogsRepository $br): Response
    {


        return $this->render('blog/all_blogs.html.twig', [
            'controller_name' => 'BlogController',

        ]);
    }

    // ! Update blogs
    #[Route('/update', name: 'updateBlogs', requirements: ['limit' => '\d+'])]
    public function updateBlogs (Request $request, BlogsRepository $br): Response
    {
        $order = $request->query->get('order', 'DESC'); // default DESC
        $blogs = $br->findAllSortedByDate($order);

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
            'order' => $order,
        ]);
    }


    // ! Create a blog
    #[Route('/create', name: 'create')]
    public function createBlog (Request $request, EntityManagerInterface $entityManager): Response
    {
        // creates a blog object and initializes inputs
        $blog = new Blog();
        $blog->setTitle('');
        $blog->setContent('');
        $blog->setCreatedAt(new DateTimeImmutable());
        $blog->setIsPublished(false);


        // Create the form using the BlogType form class
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        // Handle the form submission, validation, and saving the data to the database
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Blog added successfully!');

            return $this->redirectToRoute('blog.updateBlogs');
        }


        // return new Response('Saved new blog with id ' . $blog->getId());
        return $this->render('blog/createBlog.html.twig', [
            'formBlog' => $form
        ]);
    }

    // ! Delete a Blog
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function deleteBlog (int $id, BlogsRepository $br, EntityManagerInterface $entityManager): Response
    {
        $blog = $br->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        $entityManager->remove($blog);
        $entityManager->flush();

        $this->addFlash('success', 'Blog deleted successfully!');

        return $this->redirectToRoute('blog.updateBlogs'); // back to blog list
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

    //! Fetch All Blog Categories
    #[Route('/categories', name: 'allBlogCategories')]
    public function blogCategories (BlogCategoriesRepository $bcr): Response
    {
        $blogCategories = $bcr->findAll();


        return $this->render('blog/categories.html.twig', [
            'blogCategories' => $blogCategories
        ]);
    }

    //! Create a Blog Category
}
