<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Contact;
use App\Entity\Editor;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Biblios') //titre de la page           
            ->renderContentMaximized();

    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::linkTocrud('Utilisateurs', 'fa-regular fa-user', User::class);

        yield MenuItem::linkToCrud('Livres', 'fa-solid fa-book', Book::class);

        yield MenuItem::linkToCrud('Auteurs', 'fa-solid fa-star', Author::class);

        yield MenuItem::linkToCrud('Editeurs', 'fa-solid fa-pen', Editor::class);

        yield MenuItem::linkToCrud('Messages', 'fa-solid fa-envelope', Contact::class);

        yield MenuItem::linkToRoute('Quitter', 'fa ####', "app_main_index");

    }

    // public function configureUserMenu(UserInterface $user): UserMenu
    // {
       
    //     return parent::configureUserMenu($user)
    //         // use the given $user object to get the user name
    //         ->setName($user->getFirstname()." ".$user->getLastname())
    //         // use this method if you don't want to display the name of the user

    //         // use this method if you don't want to display the user image
    //         // you can also pass an email address to use gravatar's service
    //         ->setGravatarEmail($user->getEmail())

    //         // you can use any type of menu item, except submenus
    //         ->addMenuItems([
    //             MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
    //             MenuItem::section(),
    //         ]);
    // }
}
