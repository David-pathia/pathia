<?php

namespace App\DataFixtures;

use App\Entity\Feature;
use App\Entity\Newsletter;
use App\Entity\NewsletterSource;
use App\Entity\Subscription;
use App\Entity\SubscriptionPlan;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        // --------------------------
        // FEATURES
        // --------------------------
        $fNewsletter = (new Feature())->setName('Newsletter IA hebdomadaire');
        $fConf = (new Feature())->setName('Invitations conférences (4/an)');
        $fVisio = (new Feature())->setName('Visio 45 minutes');

        $manager->persist($fNewsletter);
        $manager->persist($fConf);
        $manager->persist($fVisio);

        // --------------------------
        // PLANS
        // --------------------------
        // Abonnement 1 : 1 newsletter / mois - 12€
        $planEssentiel = (new SubscriptionPlan())
            ->setName('Essentiel Veille')
            ->setPrice(12)
            ->setDescription("1 newsletter / mois (veille réglementaire & IA)")
            ->setEnligne(true)
        ;
        $planEssentiel->addFeature($fNewsletter);

        // Abonnement 2 : newsletter + conférences (4/an) - 19€
        $planPro = (new SubscriptionPlan())
            ->setName('Veille + Conférences')
            ->setPrice(19)
            ->setDescription("Newsletter + invitations à une série de conférences (4/an)")
            ->setEnligne(true)
        ;
        $planPro->addFeature($fNewsletter);
        $planPro->addFeature($fConf);

        // Visio 45 min - 60€ (modélisé comme un plan)
        $planVisio = (new SubscriptionPlan())
            ->setName('Consulting Visio 45 minutes')
            ->setPrice(60)
            ->setDescription("1 visio de 45 minutes (conseil & audit rapide)")
            ->setEnligne(true)
        ;
        $planVisio->addFeature($fVisio);

        $manager->persist($planEssentiel);
        $manager->persist($planPro);
        $manager->persist($planVisio);

        // --------------------------
        // USERS
        // --------------------------
        $admin = (new User())
            ->setEmail('admin@pathia.fr')
            ->setRoles(['ROLE_ADMIN'])
        ;
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setCreatedAt($now)->setUpdateAt($now);
        $ironman = (new User())
            ->setEmail('ironman@pathia.fr')
            ->setRoles(['ROLE_USER'])
        ;
        $ironman->setPassword($this->passwordHasher->hashPassword($ironman, 'password'));
        $ironman->setCreatedAt($now)->setUpdateAt($now);
        $toto = (new User())
            ->setEmail('toto@pathia.fr')
            ->setRoles(['ROLE_USER'])
        ;
        $toto->setPassword($this->passwordHasher->hashPassword($toto, 'password'));
        $toto->setCreatedAt($now)->setUpdateAt($now);
        $manager->persist($admin);
        $manager->persist($ironman);
        $manager->persist($toto);

        // --------------------------
        // SUBSCRIPTIONS
        // --------------------------
        // Ironman abonné (choix : plan Pro = newsletter + conf)
        $subIronman = (new Subscription())
            ->setName('Abonnement Ironman (test)')
            ->setUser($ironman)
            ->setSubscriptionplan($planPro)  // ou $planEssentiel si tu préfères
            ->setEtat(true)
            ->setStartedAt($now)
            ->setEndedAt($now->modify('+30 days'))
        ;
        $manager->persist($subIronman);

        // Admin : pas de subscription (il reste admin)
        // Toto : pas de subscription (non abonné)

        // --------------------------
        // NEWSLETTERS (3)
        // --------------------------
        $nl1 = (new Newsletter())
            ->setTitle("Veille IA & Formation — Ce que 2026 change déjà pour les formateurs")
            ->setSlug("veille-ia-formation-2026")
            ->setExcerpt("L’IA transforme la formation : transparence, exigences qualité et bonnes pratiques. Voici l’essentiel pour anticiper les changements…")
            ->setContent("Contenu complet premium : AI Act, RGPD, responsabilités pédagogiques, recommandations opérationnelles et check-list Qualiopi.\n\nConclusion : intégrer l’IA de façon responsable, traçable et stratégique.")
            ->setIsPublished(true)
            ->setPublishedAt($now->modify('-2 days'))
        ;
        $nl1->addSource((new NewsletterSource())
            ->setLabel("Commission européenne — AI Act / stratégie numérique")
            ->setUrl("https://digital-strategy.ec.europa.eu/")
            ->setPublisher("Commission européenne")
        );
        $nl1->addSource((new NewsletterSource())
            ->setLabel("CNIL — Intelligence artificielle & RGPD")
            ->setUrl("https://www.cnil.fr/fr/intelligence-artificielle")
            ->setPublisher("CNIL")
        );

        $nl2 = (new Newsletter())
            ->setTitle("Qualiopi + IA — Comment documenter l’usage de l’IA sans se mettre en risque")
            ->setSlug("qualio-ai-documenter-usage")
            ->setExcerpt("Comment intégrer l’IA (supports, exercices, feedback) tout en restant conforme ? Voici une méthode simple et actionnable…")
            ->setContent("Contenu complet premium : matrice 'outil / usage / données / risques / preuves', modèles de charte IA apprenants, et trame de justificatifs pour audit.")
            ->setIsPublished(true)
            ->setPublishedAt($now->modify('-1 day'))
        ;
        $nl2->addSource((new NewsletterSource())
            ->setLabel("France Compétences — Références qualité / formation pro")
            ->setUrl("https://www.francecompetences.fr/")
            ->setPublisher("France Compétences")
        );

        $nl3 = (new Newsletter())
            ->setTitle("Outils pédagogiques IA — 7 workflows concrets (sans bullshit) pour gagner du temps")
            ->setSlug("outils-ia-workflows-formateurs")
            ->setExcerpt("De la préparation de séances à la remédiation : 7 workflows IA utilisables dès demain, avec garde-fous et bonnes pratiques…")
            ->setContent("Contenu complet premium : prompts structurés, modèles de grilles d’évaluation, génération de quiz, différenciation, et méthode d’anti-hallucination.")
            ->setIsPublished(true)
            ->setPublishedAt($now)
        ;
        $nl3->addSource((new NewsletterSource())
            ->setLabel("Symfony UX / AssetMapper — bonnes pratiques front")
            ->setUrl("https://symfony.com/doc/current/frontend.html")
            ->setPublisher("Symfony")
        );

        $manager->persist($nl1);
        $manager->persist($nl2);
        $manager->persist($nl3);

        $manager->flush();
    }
}
