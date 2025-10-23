<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index (): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    //! Update user's status
    #[Route('/user-status/{id}', name: 'userStatus', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function updateUserStatus (int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $user->setIsActive(!$user->isActive());
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'User status updated successfully!');

        return $this->redirectToRoute('dashboard.users');
    }
}
