<?php

namespace App\Controller;

use App\Repository\BlogsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index (BlogsRepository $br): Response
    {
        $blogs = $br->findAll();


        return $this->render(
            'home/index.html.twig', [
            'controller_name' => 'MainController',
            'blogs' => $blogs
        ]);
    }
}
