<?php

namespace App\Controller;

use App\Entity\Blog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    //    private array $blogs = [
    //        [
    //            'blogId' => 1,
    //            'blogName' => 'Javascript',
    //            'author' => 'John Doe',
    //            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod',
    //            'created_at' => '2025/09/11',
    //        ],
    //        [
    //            'blogId' => 2,
    //            'blogName' => 'PHP',
    //            'author' => 'Jane Smith',
    //            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod',
    //            'created_at' => '2025/09/04',
    //        ],
    //    ];

    // ! All blogs
    //    #[Route('blog/{limit?}', name: 'blog', requirements: ['limit' => '\d+'])]
    //    public function index(int $limit = 2): Response
    //    {
    //        return $this->render('blog/index.html.twig', [
    //            'controller_name' => 'BlogController',
    //            'all_blogs' => $this->blogs,
    //            'pagination_limit' => $limit,
    //        ]);
    //    }

    #[Route('blogs', name: 'blogs', requirements: ['limit' => '\d+'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog();
        $blog->setTitle('My first blog');
        $blog->setContent('Long txt here');

        //        $entityManager->persist($blog);
        //        $entityManager->flush();
        $getBlog = $entityManager->getRepository(Blog::class)->findAll();


        //        return new Response('Saved new blog with id '. $getBlog);
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'all_blogs' => $getBlog,
        ]);
    }

    // ! One blog page
    #[Route('/blog/{id}', name: 'one_blog', requirements: ['id' => '\d+'])]
    public function oneBlog(int $id): Response
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
