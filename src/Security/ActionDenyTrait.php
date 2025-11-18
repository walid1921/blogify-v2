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
            throw $this->createAccessDeniedException('Access denied.');
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

        // --- RULE 1: No one can manage an admin’s blog except that admin ---
        if (
            $author instanceof User &&
            in_array('ROLE_ADMIN', $author->getRoles(), true) &&
            $author->getId() !== $current->getId()
        ) {
            throw $this->createAccessDeniedException('Access denied.');
        }

        // --- RULE 2: Bloggers can manage only their own blogs ---
        if (
            !in_array('ROLE_ADMIN', $current->getRoles(), true) && // current user is NOT admin
            $author !== $current                                  // and doesn't own the blog
        ) {
            throw $this->createAccessDeniedException('Access denied.');
        }

        // If none of the rules block → access allowed
    }


}
