<?php

namespace App\Twig;

use App\Repository\BlogCategoriesRepository;
use App\Repository\BlogsRepository;
use App\Repository\UserRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class BlogCategoriesExtension extends AbstractExtension implements GlobalsInterface
{
    private BlogCategoriesRepository $blogCategoriesRepository;
    private UserRepository $userRepository;
    private BlogsRepository $blogsRepository;

    public function __construct (BlogCategoriesRepository $blogCategoriesRepository, UserRepository $userRepository, BlogsRepository $blogsRepository)
    {
        $this->blogCategoriesRepository = $blogCategoriesRepository;
        $this->userRepository = $userRepository;
        $this->blogsRepository = $blogsRepository;
    }

    public function getGlobals (): array
    {
        return [
            // this will be available in ALL twig templates
            'blogCategories' => $this->blogCategoriesRepository->findAll(),
            'blogCategoriesCount' => $this->blogCategoriesRepository->count(),
            'blogsCount' => $this->blogsRepository->count(),
            'usersCount' => $this->userRepository->count(),
        ];
    }
}
