<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogCategoriesRepository;
use App\Repository\BlogsRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
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

    // ! Blogs table (to update)
    #[Route('/blogs', name: 'blogsTable', requirements: ['limit' => '\d+'])]
    public function blogsTable (Request $request, BlogsRepository $br): Response
    {
        $order = $request->query->get('order', 'DESC'); // default DESC
        $blogs = $br->findAllSortedByDate($order);

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
            'order' => $order,
        ]);
    }

    // ! Create a blog

    /**
     * @throws JsonException
     */
    #[Route('/create', name: 'create')]
    public function createBlog (Request $request, EntityManagerInterface $entityManager): Response
    {
        // creates a blog object and initializes inputs
        $blog = new Blog();
        $blog->setTitle('');
        $blog->setContent(json_encode(['blocks' => []], JSON_THROW_ON_ERROR)); // initialize with an empty JSON string instead of '':
        $blog->setCreatedAt(new DateTimeImmutable());
        $blog->setLikes(0);
        $blog->setIsPublished(false);


        // Create the form using the BlogType form class
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        // Handle the form submission, validation, and saving the data to the database
        if ($form->isSubmitted() && $form->isValid()) {
            // $blog = $form->getData(); // holds the submitted values
            // dd($blog); // dump and die, to see the blog object with submitted data

            // ✅ Handle uploaded cover image
            $imageFile = $form->get('coverImage')->getData();
            if ($imageFile) {
                // create a unique name for the file
                $newFilename = uniqid('', true) . '.' . $imageFile->guessExtension();

                // move the file to /public/uploads/blogs
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/blogs',
                        $newFilename
                    );
                } catch (Exception $e) {
                    $this->addFlash('danger', 'Image upload failed: ' . $e->getMessage());
                }

                // set filename in the entity
                $blog->setCoverImage($newFilename);
            }

            $entityManager->persist($blog); // presist is like prepare in SQL statements, it tells Doctrine to manage the entity and track changes to it for future database operations.
            $entityManager->flush(); // flush actually executes the SQL queries to synchronize the in-memory state of managed entities with the database.

            $this->addFlash('success', 'Blog added successfully!');
            return $this->redirectToRoute('blog.blogsTable');
        }


        // return new Response('Saved new blog with id ' . $blog->getId());
        return $this->render('blog/createBlog.html.twig', [
//            'formBlog' => $form
            'formBlog' => $form->createView(), // return the FormView so submitted values are preserved

        ]);
    }

    //! Edit a Blog
    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function editBlog (int $id, Request $request, BlogsRepository $br, EntityManagerInterface $entityManager): Response
    {
        $blog = $br->find($id);
        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Blog updated successfully!');

            return $this->redirectToRoute('blog.blogsTable');
        }
        return $this->render('blog/updateBlog.html.twig', [
            'formBlog' => $form,
            'blog' => $blog
        ]);
    }

    //! Update blog's status
    #[Route('/blog-status/{id}', name: 'blogStatus', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function updateBlogStatus (int $id, BlogsRepository $br, EntityManagerInterface $entityManager): Response
    {
        $blog = $br->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        $blog->setIsPublished(!$blog->isPublished());
        $entityManager->persist($blog);
        $entityManager->flush();

        $this->addFlash('info', 'Blog status updated successfully!');

        return $this->redirectToRoute('blog.blogsTable');
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

        return $this->redirectToRoute('blog.blogsTable');
    }

    // ! One blog page
    #[Route('/{id}', name: 'one_blog', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function oneBlog (int $id, BlogsRepository $br): Response
    {

        $blog = $br->find($id);

        // Here should redirect to not found page
        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        return $this->render('blog/oneBlog.html.twig', [
            'blog' => $blog,
        ]);
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
