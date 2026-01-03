<?php

namespace App\DataFixtures;

use App\Entity\Feature;
use App\Entity\SubscriptionPlan;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Définition des briques de services (Features)
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

        // 2. Configuration des 4 Protocoles (Plans)
        $plansConfig = [
            [
                'name'    => 'L\'Essentiel Veille',
                'price'   => 15,
                'enligne' => true,
                'features'=> ['news_ia', 'veille_reg', 'veille_ped']
            ],
            [
                'name'    => 'Le Praticien',
                'price'   => 35,
                'enligne' => true,
                'features'=> ['news_ia', 'veille_reg', 'veille_ped', 'conf', 'replay']
            ],
            [
                'name'    => 'Le Club PathIA',
                'price'   => 65,
                'enligne' => true,
                'features'=> ['news_ia', 'veille_reg', 'veille_ped', 'conf', 'replay', 'club', 'meetup']
            ],
            [
                'name'    => 'Expertise Duo',
                'price'   => 120,
                'enligne' => true,
                'features'=> ['news_ia', 'veille_reg', 'veille_ped', 'conf', 'replay', 'club', 'meetup', 'visio']
            ],
        ];

        foreach ($plansConfig as $config) {
            $plan = new SubscriptionPlan();
            $plan->setName($config['name']);
            $plan->setPrice($config['price']);
            $plan->setEnligne($config['enligne']); // Propriété en minuscule
            $plan->setDescription('Accès protocole ' . $config['name']);

            // Liaison des fonctionnalités au plan
            foreach ($config['features'] as $featureKey) {
                if (isset($featureEntities[$featureKey])) {
                    $plan->addFeature($featureEntities[$featureKey]);
                }
            }

            $manager->persist($plan);
        }

        // Envoi en base de données
        $manager->flush();
    }
}
