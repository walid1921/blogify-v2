<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'newsletter_subscribe', methods: ['POST'])]
    public function newsletter (
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $newsletter = new Newsletter();

        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($newsletter);
            $entityManager->flush();

            $this->addFlash('success', 'Thanks for joining our newsletter!');
        } else {
            if ($form->get('email')->getErrors(true)->count() > 0) {
                foreach ($form->get('email')->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->redirect(
                    $request->headers->get('referer') ?? '/'
                );
            }

            $this->addFlash('error', 'Please enter a valid email.');
        }

        return $this->redirect(
            $request->headers->get('referer') ?? '/'
        );
    }
}
