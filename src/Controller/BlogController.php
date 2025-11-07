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

    /**
     * @throws JsonException
     */
    #[Route('/', name: 'allBlogs', requirements: ['limit' => '\d+'])]
    public function index (BlogsRepository $blogRepo): Response
    {


        // Fetch blogs from the repository
        $blogs = $blogRepo->findAllPublished();

        foreach ($blogs as $blog) {
            $json = json_decode($blog->getContent() ?? '""', true, 512, JSON_THROW_ON_ERROR) ?? [];

            $blog->excerpt = '';

            foreach ($json['blocks'] ?? [] as $block) {
                if (($block['type'] ?? '') === 'paragraph' && !empty($block['data']['text'])) {
                    $blog->excerpt = $block['data']['text'];
                    break;
                }
            }
        }


        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
//            'blogs' => $this->dummyBlogs,
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
