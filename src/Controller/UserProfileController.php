<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\UserProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index (EntityManagerInterface $em, UserProfileRepository $profileRepo): Response
    {

        $user = new User();
//        $user->setEmail($this->getUser()->getEmail());
        $user->setUsername("walid");
        $user->setEmail("walid@gmail.com");
        $user->setPassword("walid1921");

        $profile = new UserProfile();
        $profile->setUser($user);

        $em->persist($profile);
        $em->flush();

        return $this->render('user_profile/index.html.twig', [
            'limit' => 3
        ]);
    }
}
