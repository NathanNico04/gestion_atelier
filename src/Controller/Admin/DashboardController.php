<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Routing\Attribute\Route;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
        // the name visible to end users
        ->setTitle('Administration')

        // there's no need to define the "text direction" explicitly because
        // its default value is inferred dynamically from the user locale
        ->setTextDirection('ltr')
        // by default, the UI color scheme is 'auto', which means that the backend
        // will use the same mode (light/dark) as the operating system and will
        // change in sync when the OS mode changes.
        // Use this option to set which mode ('light', 'dark' or 'auto') will users see
        // by default in the backend (users can change it via the color scheme selector)
        ->setDefaultColorScheme('light')
        // instead of magic strings, you can use constants as the value of
        // this option: EasyCorp\Bundle\EasyAdminBundle\Config\Option\ColorScheme::DARK
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de Bord', 'fa fa-list');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
    }
}
