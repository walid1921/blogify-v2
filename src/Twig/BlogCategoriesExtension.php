<?php

namespace App\Twig;

use App\Repository\BlogCategoriesRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class BlogCategoriesExtension extends AbstractExtension implements GlobalsInterface
{
    private BlogCategoriesRepository $repo;

    public function __construct (BlogCategoriesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getGlobals (): array
    {
        return [
            // this will be available in ALL twig templates
            'blogCategories' => $this->repo->findAll(),
        ];
    }
}
