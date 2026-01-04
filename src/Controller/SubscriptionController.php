<?php

namespace App\Controller;

use App\Repository\SubscriptionPlanRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SubscriptionController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    #[Route('/pricing', name: 'app_pricing')]
    public function index(SubscriptionPlanRepository $planRepo): Response
    {
        // Log de consultation (Marketing)
        $this->logger->info('Accès au catalogue des tarifs.', [
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme'
        ]);

        return $this->render('subscription/pricing.html.twig', [
            'plans' => $planRepo->findBy(['enligne' => true]),
        ]);
    }

    /**
     * Exemple de route protégée par une Feature précise
     */
    #[Route('/premium/newsletter', name: 'app_premium_newsletter')]
    #[IsGranted('ROLE_USER')] // Sécurité 1 : Doit être connecté
    public function newsletter(): Response
    {
        // Sécurité 2 : Le Voter vérifie si le plan de l'user contient la feature
        // Si non, Symfony renvoie automatiquement une erreur 403 (Forbidden)
        $this->denyAccessUnlessGranted('ACCESS_FEATURE', 'Newsletter IA hebdomadaire');

        $this->logger->info('Accès autorisé à la Newsletter IA.', [
            'user' => $this->getUser()->getUserIdentifier()
        ]);

        return $this->render('subscription/newsletter.html.twig');
    }
    #[Route('/subscribe/process/{id}', name: 'app_subscribe_process')]
    #[IsGranted('ROLE_USER')]
    public function process(int $id, SubscriptionPlanRepository $planRepo): Response
    {
        $plan = $planRepo->find($id);
        $user = $this->getUser();

        // 1. Vérification d'existence
        if (!$plan || !$plan->isEnligne()) {
            $this->logger->alert('TENTATIVE DE FRAUDE : Plan inexistant ou hors-ligne demandé.', [
                'user' => $user->getUserIdentifier(),
                'plan_id' => $id,
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            throw $this->createNotFoundException('Protocole invalide.');
        }

        // 2. Vérification de possession (Audit)
        // On vérifie si l'utilisateur possède déjà TOUTES les features de ce plan
        $hasPlan = true;
        foreach ($plan->getFeatures() as $feature) {
            if (!$this->isGranted('ACCESS_FEATURE', $feature->getName())) {
                $hasPlan = false;
                break;
            }
        }

        if ($hasPlan) {
            $this->logger->warning('TENTATIVE DE DOUBLON : L\'utilisateur tente de racheter un plan déjà actif.', [
                'user' => $user->getUserIdentifier(),
                'plan_name' => $plan->getName()
            ]);
            $this->addFlash('warning', 'Ce protocole est déjà actif sur votre armure.');
            return $this->redirectToRoute('app_pricing');
        }

        // 3. Log de succès d'étape (Prêt pour Stripe)
        $this->logger->notice('INITIATION PAIEMENT : Redirection vers Stripe.', [
            'user' => $user->getUserIdentifier(),
            'plan_name' => $plan->getName(),
            'amount' => $plan->getPrice()
        ]);

        // Bientôt : Logique Stripe ici
        return $this->render('subscription/confirm.html.twig', ['plan' => $plan]);
    }
}
