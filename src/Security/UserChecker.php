<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth (UserInterface $user): void
    {

        // Only handle our own user Entity
        if (!$user instanceof User) {
            return;
        }


        // If user is not active, block login and throw exception
        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException('Your account has been deactivated. Please contact support.');
        }
    }


    // After the user is authenticated

    public function checkPostAuth (UserInterface $user): void
    {
        // TODO: Implement checkPostAuth() method.
        // You can add extra checks here if needed (e.g. email verified)

    }

}
