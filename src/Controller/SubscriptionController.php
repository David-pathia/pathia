<?php

namespace App\Controller;

use App\Repository\SubscriptionPlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SubscriptionController extends AbstractController
{
    #[Route('/pricing', name: 'app_pricing')]
    public function index(SubscriptionPlanRepository $planRepo): Response
    {
        return $this->render('subscription/pricing.html.twig', [
            // On cherche tous les plans où 'enLigne' est égal à true (ou 1)
            'plans' => $planRepo->findBy(['enligne' => true]),
        ]);
    }
}
