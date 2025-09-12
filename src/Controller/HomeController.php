<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home_page')]
    public function index(): Response
    {
        $username = 'walid';

        return $this->render(
            'home/index.html.twig', [
                'controller_name' => 'MainController',
                'username' => $username,
            ]);
    }
}
