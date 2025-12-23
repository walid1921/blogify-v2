<?php

namespace App\Twig;

use App\Form\NewsletterType;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class NewsletterExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct (
        private FormFactoryInterface $formFactory
    )
    {
    }

    public function getGlobals (): array
    {
        return [
            'newsletterForm' => $this->formFactory
                ->create(NewsletterType::class)
                ->createView(),
        ];
    }
}
