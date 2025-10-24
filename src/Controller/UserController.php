<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index (): Response
    {
        return $this->render('user/index.html.twig', [

        ]);
    }


    // ! Create a User
    #[Route('/users/create', name: 'userCreate', methods: ['GET', 'POST'])]
    public function createUser (Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {

//        $user = new User();
//        $user->setUsername('newuser_' . rand(100, 999));
//        $user->setEmail('newuser' . rand(100, 999) . '@example.com');
//        $user->setPassword(password_hash('password123', PASSWORD_BCRYPT));
//        $user->setRoles(['ROLE_BLOGGER']);
//        $user->setIsActive(true);
//        $user->setCreatedAt(new DateTimeImmutable());

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

        return $this->render('user/create_user.html.twig', [
            'formUser' => $form->createView(),
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
