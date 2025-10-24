<?php

namespace App\Controller;

use App\Entity\BlogCategories;
use App\Form\BlogCategoriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/blog/category', name: 'app_blog_category')]
    public function index (): Response
    {
        return $this->render('blog_category/index.html.twig', [
            'controller_name' => 'BlogCategoryController',
        ]);
    }


    //! Create a Blog Category
    #[Route('/categories/create', name: 'createBlogCategories')]
    public function createCategory (Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new BlogCategories();
        $form = $this->createForm(BlogCategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully!');
            return $this->redirectToRoute('dashboard.allBlogCategories');
        }

        return $this->render('blog_category/create_category.html.twig', [
            'formCategory' => $form->createView(),
        ]);

    }
}
