<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategories;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\BlogType;
use App\Form\UserType;
use App\Repository\BlogCategoriesRepository;
use App\Repository\BlogsRepository;
use App\Repository\LikesRepository;
use App\Repository\UserRepository;
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

    private array $dummyBlogs = [
        [
            'category' => 'Event',
            'title' => 'The fascination of texture: fringes',
            'readTime' => '4 min read',
            'author' => 'Walid Ayad',
            'date' => 'Feb 20, 2025',
            'image' => 'images/252-15041-4036_2.png',
            'content' => "Welcome to REVA – where fashion isn’t just clothing, but a passion, an expression of youth, and a journey of self-expression. Since our start in 2020, we’ve been..."
        ],
        [
            'category' => 'Collection',
            'title' => 'Redefining elegance with sustainable materials',
            'readTime' => '3 min read',
            'author' => 'Walid Ayad',
            'date' => 'Mar 1, 2025',
            'image' => 'images/252-15041-4036_2.png',
            'content' => 'Our latest collection merges classic silhouettes with eco-friendly textiles, redefining elegance through sustainability...'
        ],
        [
            'category' => 'Editorial',
            'title' => 'The story behind modern craftsmanship',
            'readTime' => '5 min read',
            'author' => 'Walid Ayad',
            'date' => 'Apr 10, 2025',
            'image' => 'images/252-15041-4036_2.png',
            'content' => 'Behind every piece is a team of artisans dedicated to the perfection of detail. Explore the story of how tradition meets technology...'
        ],
        [
            'category' => 'Editorial',
            'title' => 'The story behind modern craftsmanship',
            'readTime' => '5 min read',
            'author' => 'Walid Ayad',
            'date' => 'Apr 10, 2025',
            'image' => 'images/252-15041-4036_2.png',
            'content' => 'Behind every piece is a team of artisans dedicated to the perfection of detail. Explore the story of how tradition meets technology...'
        ],
        [
            'category' => 'Editorial',
            'title' => 'The story behind modern craftsmanship',
            'readTime' => '5 min read',
            'author' => 'Walid Ayad',
            'date' => 'Apr 10, 2025',
            'image' => 'images/252-15041-4036_2.png',
            'content' => 'Behind every piece is a team of artisans dedicated to the perfection of detail. Explore the story of how tradition meets technology...'
        ],
        [
            'category' => 'Editorial',
            'title' => 'The story behind modern craftsmanship',
            'readTime' => '5 min read',
            'author' => 'Walid Ayad',
            'date' => 'Apr 10, 2025',
            'image' => 'images/252-15041-4036_2.png',
            'content' => 'Behind every piece is a team of artisans dedicated to the perfection of detail. Explore the story of how tradition meets technology...'
        ],
    ];

    // ! Fetch All Blogs
    #[Route('/', name: 'allBlogs', requirements: ['limit' => '\d+'])]
    public function index (): Response
    {


        // Fetch blogs from the repository
//        $blogs = $blogRepo->findAll();


        return $this->render('blog/index.html.twig', [
//            'blogs' => $blogs,
            'blogs' => $this->dummyBlogs,
        ]);
    }


    // ! Create a blog

    /**
     * @throws JsonException
     */
    #[Route('/create', name: 'create')]
    public function createBlog (Request $request, EntityManagerInterface $entityManager, UserRepository $userRepo): Response
    {

        $user = $userRepo->find(11);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        // creates a blog object and initializes inputs
        $blog = new Blog();
        $blog->setTitle('');
        $blog->setContent(json_encode(['blocks' => []], JSON_THROW_ON_ERROR)); // initialize with an empty JSON string instead of '':
        $blog->setCreatedAt(new DateTimeImmutable());
        $blog->setReadTime();
//        $blog->setAuthor($this->getUser());

        $blog->setAuthor($user);
        $blog->setBlogLanguage('');
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
                    $this->addFlash('error', 'Image upload failed: ' . $e->getMessage());
                }

                // set filename in the entity
                $blog->setCoverImage($newFilename);
            }

            $entityManager->persist($blog); // presist is like prepare in SQL statements, it tells Doctrine to manage the entity and track changes to it for future database operations.
            $entityManager->flush(); // flush actually executes the SQL queries to synchronize the in-memory state of managed entities with the database.

            $this->addFlash('success', 'Blog added successfully!');
            return $this->redirectToRoute('dashboard.allBlogs');
        }


        // return new Response('Saved new blog with id ' . $blog->getId());
        return $this->render('blog/createBlog.html.twig', [
//            'formBlog' => $form,
            'formBlog' => $form->createView() // return the FormView so submitted values are preserved

        ]);
    }

    //! Edit a Blog
    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function editBlog (int $id, Request $request, BlogsRepository $blogRepo, EntityManagerInterface $entityManager): Response
    {
        $blog = $blogRepo->find($id);
        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Blog updated successfully!');

            return $this->redirectToRoute('dashboard.allBlogs');
        }
        return $this->render('blog/updateBlog.html.twig', [
            'formBlog' => $form,
            'blog' => $blog
        ]);
    }

    //! Update blog's status
    #[Route('/blog-status/{id}', name: 'blogStatus', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function updateBlogStatus (int $id, BlogsRepository $blogRepo, EntityManagerInterface $entityManager): Response
    {
        $blog = $blogRepo->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        $blog->setIsPublished(!$blog->isPublished());
        $entityManager->persist($blog);
        $entityManager->flush();

        $this->addFlash('success', 'Blog status updated successfully!');

        return $this->redirectToRoute('dashboard.allBlogs');
    }

    // ! Delete a Blog
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function deleteBlog (int $id, BlogsRepository $blogRepo, EntityManagerInterface $entityManager): Response
    {
        $blog = $blogRepo->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        $entityManager->remove($blog);
        $entityManager->flush();

        $this->addFlash('success', 'Blog deleted successfully!');

        return $this->redirectToRoute('dashboard.allBlogs');
    }

    // ! One blog page
    #[Route('/{id}', name: 'one_blog', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function oneBlog (int $id, BlogsRepository $blogRepo, LikesRepository $likesRepository): Response
    {

        $blog = $blogRepo->find($id);

        // Here should redirect to not found page
        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        // Efficient way: count likes for this specific blog
        $likesCount = $likesRepository->count(['blog' => $blog]);

        // or you could use $blog->getLikes()->count(); (fine for small data)
        // $likesCount = $blog->getLikes()->count();

        return $this->render('blog/oneBlog.html.twig', [
            'blog' => $blog,
            'likesCount' => $likesCount,
        ]);
    }
}
