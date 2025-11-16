<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Blog;

trait ActionDenyTrait
{
    /**
     * Deny when trying to manage another admin (except yourself).
     */
    private function denyIfCannotManageUser (User $target): void
    {
        $current = $this->getUser();

        if (!$current instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }

        // cannot manage another admin, but you can manage yourself
        if (
            in_array('ROLE_ADMIN', $target->getRoles(), true) &&
            $target->getId() !== $current->getId()
        ) {
            throw $this->createAccessDeniedException('You cannot manage another administrator.');
        }
    }

    /**
     * Deny when trying to manage a blog:
     * - normal bloggers can manage ONLY their own blogs
     * - nobody can manage a blog owned by an admin (except that admin themselves)
     */
    private function denyIfCannotManageBlog (Blog $blog): void
    {
        $current = $this->getUser();

        if (!$current instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }

        $author = $blog->getAuthor();

        // If blog author is an admin and it's not you -> forbidden
        if (
            $author instanceof User &&
            in_array('ROLE_ADMIN', $author->getRoles(), true) &&
            $author->getId() !== $current->getId()
        ) {
            throw $this->createAccessDeniedException('You cannot manage a blog owned by an administrator.');
        }

        // If you're NOT admin, you can only manage your own blogs
        if (
            !in_array('ROLE_ADMIN', $current->getRoles(), true) &&
            $author !== $current
        ) {
            throw $this->createAccessDeniedException('You can manage only your own blogs.');
        }
    }
}
