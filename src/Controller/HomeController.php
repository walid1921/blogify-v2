<?php

namespace App\Controller;

use App\Repository\BlogsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{

    private array $dummyBlogs = [
        [
            'category' => 'Event',
            'title' => 'The fascination of texture: fringes',
            'readTime' => '4 min read',
            'author' => 'Walid Ayad',
            'date' => 'Oct 15, 2025',
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
    ];


    #[Route('/', name: 'home')]
    public function index (BlogsRepository $blogRepo): Response
    {

        //! Fetch latest published blogs
        $latestBlogs = $blogRepo->findLatestPublished();

        foreach ($latestBlogs as $blog) {
            $blog->getExcerpt();
        }


        //! Highlighting a specific blog for the home page, will be controlled later from the settings panel
        $blogHighlightedHome = $blogRepo->find(23);

        // Here should redirect to not found page
        if (!$blogHighlightedHome) {
            throw $this->createNotFoundException('Blog not found');
        }

        $blogHighlightedHome->getExcerpt();


        return $this->render('home/index.html.twig', [
            //'blogs' => $this->dummyBlogs,
            'latestBlogs' => $latestBlogs,
            'blogHighlightedHome' => $blogHighlightedHome,
        ]);

    }
}
