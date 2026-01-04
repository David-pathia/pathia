<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Subscription;
use App\Entity\SubscriptionPlan;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Redirige par défaut vers la liste des Utilisateurs
        $url = $this->adminUrlGenerator
            ->setController(UserCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span style="color: #C1121F; font-weight: bold;">PATHIA</span> <span style="color: #FDF0D5;">CORP</span>')
            ->setFaviconPath('favicon.svg')
            ->renderContentMaximized();
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            // On précise qu'on injecte dans le HEAD
            ->addHtmlContentToHead('
            <style>
                :root {
                    --accent-color: #C1121F;
                    --text-primary: #FDF0D5;
                }
                .main-header { background: #1a1a1a !important; border-bottom: 1px solid var(--accent-color); }
                .main-sidebar { background: #0B0C10 !important; border-right: 1px solid var(--accent-color); }
                .content-wrapper { background: #111 !important; color: #eee; }
                .card { background: #1a1a1a !important; border: 1px solid #333; }
                .sidebar-menu i { color: var(--accent-color) !important; }
                /* Boutons et badges */
                .btn-primary { background-color: var(--accent-color) !important; border-color: var(--accent-color) !important; }
                .badge-success { background-color: #28a745 !important; }
            </style>
        ');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au Site', 'fas fa-eye', 'app_home');

        yield MenuItem::section('Contrôle des Unités');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user-shield', User::class);

        yield MenuItem::section('Protocoles Financiers');
        yield MenuItem::linkToCrud('Abonnements', 'fas fa-file-invoice-dollar', Subscription::class);
        yield MenuItem::linkToCrud('Plans Tarifaires', 'fas fa-layer-group', SubscriptionPlan::class);

        yield MenuItem::section('Système');
        yield MenuItem::linkToLogout('Déconnexion', 'fas fa-sign-out-alt');
    }
}
