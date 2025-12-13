<?php

namespace App\Controller\Admin;

use App\Entity\User;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
               ->setEntityLabelInSingular('Utilisateur')
               ->setEntityLabelInPlural('Utilisateurs')
               ->setPaginatorPageSize(10)
               ->setPageTitle("index", "Nos utilisateurs")
               ->setPageTitle('detail', fn (User $user) => "Détails - ".(string)$user->__toString())
               ->setPageTitle('edit', fn (User $user) => "Edition - ".(string)$user->__toString())

        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
            ->hideOnForm()
            ,
            EmailField::new("email")
            ,
            TextField::new('firstname', "Nom"),
            TextField::new('lastname', "Prenom"),
            TextField::new("username", "Nom d'utilisateur"),
            ArrayField::new("roles")
            ->hideOnIndex()
            ,

            DateTimeField::new("ConnectedAt", "Dernière Connexion")
            ->hideOnForm()
            ,

            TextField::new("Plainpassword")
            ->onlyWhenCreating(),

        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

}
