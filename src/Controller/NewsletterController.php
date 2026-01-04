<?php
// src/Controller/NewsletterController.php
namespace App\Controller;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NewsletterController extends AbstractController
{
    #[Route('/newsletters/{slug}', name: 'newsletter_show')]
    public function show(string $slug, NewsletterRepository $repo): Response
    {
        $newsletter = $repo->findOneBy(['slug' => $slug, 'isPublished' => true]);
        if (!$newsletter) {
            throw $this->createNotFoundException();
        }

        $hasFullAccess = $this->isGranted('ACCESS_FEATURE', 'Newsletter IA hebdomadaire');

        return $this->render('newsletter/show.html.twig', [
            'newsletter' => $newsletter,
            'hasFullAccess' => $hasFullAccess,
        ]);
    }
    #[Route('/newsletters', name: 'app_newsletter_index')]
    #[IsGranted('ROLE_USER')]
    public function index(NewsletterRepository $repo): Response
    {
        $newsletters = $repo->findBy(
            ['isPublished' => true],
            ['publishedAt' => 'DESC']
        );

        $hasFullAccess = $this->isGranted('ACCESS_FEATURE', 'Newsletter IA hebdomadaire');

        return $this->render('newsletter/index.html.twig', [
            'newsletters' => $newsletters,
            'hasFullAccess' => $hasFullAccess,
        ]);
    }
}

