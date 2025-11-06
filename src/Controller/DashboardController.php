<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategories;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\BlogCategoriesType;
use App\Form\BlogType;
use App\Form\UserType;
use DateTimeImmutable;
use DateTime;
use App\Repository\BlogCategoriesRepository;
use App\Repository\BlogsRepository;
use App\Repository\LikesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
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

    // ! Create a blog

    /**
     * @throws JsonException
     */
    #[Route('create', name: 'createBlog')]
    public function createBlog (Request $request, EntityManagerInterface $entityManager, UserRepository $userRepo): Response
    {

        $user = $userRepo->find(16);

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
        return $this->render('dashboard/index.html.twig', [
//            'formBlog' => $form,
            'formBlog' => $form->createView() // return the FormView so submitted values are preserved

        ]);
    }

    //! Edit a Blog
    #[Route('/edit/{id}', name: 'editBlog', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
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
        return $this->render('dashboard/index.html.twig', [
            'formBlog' => $form,
            'blog' => $blog
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

    //! Create a Blog Category
    #[Route('/categories/create', name: 'createBlogCategories')]
    public function createCategory (Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new BlogCategories();
        $category->setCreatedAt(new DateTime());

        $form = $this->createForm(BlogCategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully!');
            return $this->redirectToRoute('dashboard.allBlogCategories');
        }

        return $this->render('dashboard/index.html.twig', [
            'formCategory' => $form->createView(),
        ]);

    }

    //! Edit a Blog Category
    #[Route('/categories/edit/{id}', 'editBlogCategories', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function editCategory (int $id, Request $request, EntityManagerInterface $entityManager, BlogCategoriesRepository $blogCategoriesRepository): Response
    {

        $category = $blogCategoriesRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(BlogCategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully!');

            return $this->redirectToRoute('dashboard.allBlogCategories');
        }

        return $this->render('dashboard/index.html.twig', [
            'formCategory' => $form->createView(),
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

    // ! Create a User
    #[Route('/users/create', name: 'userCreate', methods: ['GET', 'POST'])]
    public function createUser (Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $user->getPassword();
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);
            $user->setIsActive(true);
            $user->setCreatedAt(new DateTimeImmutable());

            $profile = new UserProfile();
            $profile->setUser($user);
            $profile->setCreatedAt(new DateTimeImmutable());

            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'User created successfully!');
            return $this->redirectToRoute('dashboard.users');
        }

        return $this->render('dashboard/index.html.twig', [
            'formUser' => $form->createView(),
        ]);
    }

    //! Edit a user
    #[Route('/users/edit/{id}', name: 'userEdit', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function editUser (int $id, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User updated successfully!');

            return $this->redirectToRoute('dashboard.users');
        }

        return $this->render('dashboard/index.html.twig', [
            'formUser' => $form->createView(),
            'username' => $user->getUsername(),
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
