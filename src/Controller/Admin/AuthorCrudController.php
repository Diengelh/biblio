<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Entity\Editor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class AuthorCrudController extends AbstractCrudController
{   
    public static function getEntityFqcn(): string
    {
        return Author::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
               ->setEntityLabelInSingular('Auteur')
               ->setEntityLabelInPlural('Auteurs')
               ->setPaginatorPageSize(10)
               ->setPageTitle("index", "Auteurs")
               ->setPageTitle('detail', fn (Author $author) => "Détails - ".(string)$author->getName())
               ->setPageTitle('edit', fn (Author $author) => "Edition - ".(string)$author->getName())

        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
            ->hideOnForm()
            ,

            TextField::new('name', "Nom"),

            DateField::new('dateOfBirth', "Date de naissance"),

            TextField::new("nationality", "Nationalité"),

            ArrayField::new("books", "Livre(s)")
            ->hideOnForm()
        ];
    }

    public function ConfigureActions(Actions $actions) : Actions
    {
        return $actions
               ->set(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
