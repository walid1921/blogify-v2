<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserProfile;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct (private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load (ObjectManager $manager): void
    {
        $faker = Factory::create();


        $user = new User();
        $user->setUsername($faker->userName());
        $user->setEmail($faker->email());
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                'AdminPassword123'
            )
        );
        $user->setIsActive(true);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setTerms(true);

        $profile = new UserProfile();
        $profile->setUser($user);
        $profile->setCreatedAt(new DateTimeImmutable());

        $manager->persist($user);
        $manager->persist($profile);


        // A loop that Generate 100 blogs
//        for ($i = 0; $i < 20; $i++) {
//            $user = new User();
//            $user->setUsername($faker->userName());
//            $user->setEmail($faker->email());
//            $user->setRoles(['ROLE_BLOGGER']);
//
//
//            $user->setPassword(
//                $this->passwordHasher->hashPassword(
//                    $user,
//                    'password123'
//                )
//            );
//            $user->setIsActive(true);
//            $user->setCreatedAt(new DateTimeImmutable());
//            $user->setTerms(true);
//
//            $profile = new UserProfile();
//            $profile->setUser($user);
//            $profile->setCreatedAt(new DateTimeImmutable());
//
//
//            $manager->persist($user);
//            $manager->persist($profile);
//        }

        $manager->flush();
    }
}
