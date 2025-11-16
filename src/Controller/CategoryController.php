<?php

namespace App\Controller;

use App\Entity\BlogCategories;
use App\Form\BlogCategoriesType;
use App\Repository\BlogCategoriesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryController extends AbstractController
{
    #[isGranted('ROLE_ADMIN')]
    #[Route('/blog/category', name: 'app_blog_category')]
    public function index (): Response
    {
        return $this->render('blog_category/index.html.twig', [
            'controller_name' => 'BlogCategoryController',
        ]);
    }

    //! Delete a Category
    #[isGranted('ROLE_ADMIN')]
    #[Route('/categories/delete/{id}', name: 'deleteBlogCategories', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function deleteCategory (int $id, EntityManagerInterface $entityManager, BlogCategoriesRepository $blogCategoriesRepository): Response
    {

        $category = $blogCategoriesRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success', 'Category deleted successfully!');

        return $this->redirectToRoute('dashboard.allBlogCategories');
    }
}
