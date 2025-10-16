<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    private array $users = [
//        [
//            'username' => 'walid',
//            'age' => 27,
//        ],
//        [
//            'username' => 'Ben',
//            'age' => 30,
//        ],
    ];


    #[Route('/users', name: 'users')]
    public function index (): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $this->users,
        ]);
    }
}
