<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class EditorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Editor::class;
    }

    public function ConfigureCrud(Crud $crud): Crud
    {
        return $crud    
               ->setPaginatorPageSize(10)
               ->setEntityLabelInPlural("Editeurs")
               ->setEntityLabelInSingular("Editeur")
               ->setPageTitle('edit', fn (Editor $editor) => "Edition - ".(string)$editor->getName())
               ->setPageTitle('detail', fn (Editor $editor) => "Détails - ".(string)$editor->getName());

    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions 
               ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
            ->hideOnForm(),

            TextField::new('name', "Nom"),

            ArrayField::new('books', "Livre(s)")
            ->hideOnForm()
            ,
        ];
    }
}
