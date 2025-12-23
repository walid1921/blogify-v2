<?php

namespace App\Tests\Unit\Entity;


use App\Entity\User;
use App\Entity\UserProfile;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class UserTest extends TestCase
{

    public function testCreate (): void
    {
        //! Arrange
// $user = new User();

        //! Action
        $this->user->setUsername('newuser1921');
        $this->user->setEmail('newuser@example.com');
        $this->user->setPassword(password_hash('AdminPassword123', PASSWORD_BCRYPT));
        $this->user->setRoles(['ROLE_BLOGGER', 'ROLE_USER']);
        $this->user->setIsActive(true);
        $this->user->setCreatedAt(new DateTimeImmutable());
        $this->user->setTerms(true);


        //! Assert
        self::assertSame('newuser1921', $this->user->getUsername(), 'username should be set');
        self::assertSame('newuser@example.com', $this->user->getEmail());

        $roles = $this->user->getRoles();
        self::assertContains('ROLE_BLOGGER', $roles);
        self::assertContains('ROLE_USER', $roles);

        self::assertTrue($this->user->isActive());
    }


    protected function setUp (): void
    {
        parent::setUp();

        $this->user = new User();
    }
}
