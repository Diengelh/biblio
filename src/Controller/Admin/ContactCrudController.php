<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Contact')
            ->setEntityLabelInPlural('Contacts')
            ->setPaginatorPageSize(10)
            ->setPageTitle("index", "Messages")
            ->setPageTitle('detail', fn (Contact $contact) => "Détails - ".(string)$contact->   getSubject())
            ->setPageTitle('edit', fn (Contact $contact) => "Edition - ".(string)$contact->getSubject())
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
               ->add(Crud::PAGE_INDEX, Action::DETAIL)
               ->remove(Crud::PAGE_INDEX, Action::EDIT)
               ->remove(Crud::PAGE_INDEX, Action::NEW)
               ->remove(Crud::PAGE_DETAIL, Action::EDIT)
               ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('name', "Nom"),

            TextField::new('firstname', "Prenom"),

            EmailField::new('email', "Email"),

            TextField::new("subject", "Sujet"),

            TextAreaField::new("message", "Message"),
            
        ];
    }
}
