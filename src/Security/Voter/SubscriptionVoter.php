<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubscriptionVoter extends Voter
{
    // L'attribut que nous allons vérifier
    public const ACCESS_FEATURE = 'ACCESS_FEATURE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // On ne supporte que l'attribut ACCESS_FEATURE avec un nom de service en sujet
        return $attribute === self::ACCESS_FEATURE && is_string($subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) return false;

        $subscription = $user->getActiveSubscription();

        // On vérifie juste si l'abonnement existe (la validité est déjà gérée par getActiveSubscription)
        if (!$subscription) {
            return false;
        }

        // Attention au nom de la propriété : subscriptionplan (tout en minuscules dans ton entité)
        $plan = $subscription->getSubscriptionplan();

        foreach ($plan->getFeatures() as $feature) {
            if ($feature->getName() === $subject) {
                return true;
            }
        }

        return false;
    }
}
