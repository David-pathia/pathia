<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException('Compte désactivé.');
        }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Merci de vérifier votre email avant de vous connecter.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
