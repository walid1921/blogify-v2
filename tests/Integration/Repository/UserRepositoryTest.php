<?php

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class UserRepositoryTest extends KernelTestCase
{

    protected static function getKernelClass (): string
    {
        return Kernel::class;
    }

    public function testUserIsCreatedWithProfile (): void
    {

        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setUsername('newuser2000');
        $user->setEmail('newuser2455sdgdsg0@example.com');
        $user->setPassword(password_hash('AdminPassword123', PASSWORD_BCRYPT));
        // $user->setRoles(['ROLE_BLOGGER', 'ROLE_USER']);
        $user->setIsActive(true);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setTerms(true);

        $profile = new UserProfile();
        $profile->setUser($user);
        $profile->setCreatedAt(new DateTimeImmutable());

        $entityManager->persist($user);
        $entityManager->persist($profile);
        $entityManager->flush();
        $entityManager->clear();

        // Fetching
        $repo = $entityManager->getRepository(User::class);
        $savedUser = $repo->findOneBy(['email' => 'newuser2455400@example.com']);

        self::assertNotNull($savedUser);
        self::assertNotNull($savedUser->getUserProfile());


    }

}
