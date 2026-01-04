<?php

namespace App\DataFixtures;

use App\Entity\Feature;
use App\Entity\Subscription;
use App\Entity\SubscriptionPlan;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    // On injecte le service de hachage de Symfony
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Définition des Services (Features)
        $featuresData = [
            'news_ia'    => 'Newsletter IA hebdomadaire',
            'veille_reg' => 'Veille réglementaire & légale',
            'veille_ped' => 'Veille pédagogique & outils',
            'conf'       => 'Accès aux Conférences annuelles',
            'replay'     => 'Accès aux Replays illimités',
            'club'       => 'Accès au Club & Discord privé',
            'meetup'     => '2 RDV exclusifs / an (Échanges Philo/Tech)',
            'visio'      => '1h de Visio privée sur mesure'
        ];

        $featureEntities = [];
        foreach ($featuresData as $key => $name) {
            $feature = new Feature();
            $feature->setName($name);
            $manager->persist($feature);
            $featureEntities[$key] = $feature;
        }

        // 2. Configuration des Plans
        $plansConfig = [
            ['name' => 'L\'Essentiel Veille', 'price' => 15, 'features' => ['news_ia', 'veille_reg', 'veille_ped']],
            ['name' => 'Le Praticien', 'price' => 35, 'features' => ['news_ia', 'veille_reg', 'veille_ped', 'conf', 'replay']],
            ['name' => 'Le Club PathIA', 'price' => 65, 'features' => ['news_ia', 'veille_reg', 'veille_ped', 'conf', 'replay', 'club', 'meetup']],
            ['name' => 'Expertise Duo', 'price' => 120, 'features' => ['news_ia', 'veille_reg', 'veille_ped', 'conf', 'replay', 'club', 'meetup', 'visio']],
        ];

        $allPlans = [];
        foreach ($plansConfig as $config) {
            $plan = new SubscriptionPlan();
            $plan->setName($config['name']);
            $plan->setPrice($config['price']);
            $plan->setEnligne(true);
            $plan->setDescription('Accès protocole ' . $config['name']);
            foreach ($config['features'] as $fKey) {
                $plan->addFeature($featureEntities[$fKey]);
            }
            $manager->persist($plan);
            $allPlans[] = $plan;
        }

        // 3. CRÉATION DE L'ADMIN (Adminman)
        $admin = new User();
        $admin->setEmail('adminman@pathia.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);

        // Hachage dynamique du mot de passe "password"
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));

        if (method_exists($admin, 'setCreatedAt')) {
            $admin->setCreatedAt(new \DateTimeImmutable());
        }
        $manager->persist($admin);

        // 4. CRÉATION DE L'USER (Ironman)
        $user = new User();
        $user->setEmail('ironman@pathia.fr');
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(true);

        // Hachage dynamique du mot de passe "password"
        $user->setPassword($this->hasher->hashPassword($user, 'password'));

        if (method_exists($user, 'setCreatedAt')) {
            $user->setCreatedAt(new \DateTimeImmutable());
        }
        $manager->persist($user);

        // 5. ATTRIBUTION D'UN ABONNEMENT À IRONMAN
        $subscription = new Subscription();
        $subscription->setUser($user);
        $subscription->setSubscriptionplan($allPlans[0]);
        $subscription->setName('Contrat Test Ironman');
        $subscription->setStartedAt(new \DateTimeImmutable());
        $subscription->setEndedAt((new \DateTimeImmutable())->modify('+1 month'));
        $subscription->setEtat(true);

        if (method_exists($subscription, 'setCreatedAt')) {
            $subscription->setCreatedAt(new \DateTimeImmutable());
        }
        $manager->persist($subscription);

        $manager->flush();
    }
}
