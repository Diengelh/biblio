<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\Author;
use App\Enum\BookStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
               ->setEntityLabelInSingular('livre')
               ->setEntityLabelInPlural('livres')
               ->setPaginatorPageSize(10)
               ->setPageTitle("index", "Collection de livre")
               ->setPageTitle('detail', fn (Book $book) => "Détails - ".(string)$book->getTitle())
               ->setPageTitle('edit', fn (Book $book) => "Edition - ".(string)$book->getTitle())

            # décommenter les lignes de codes qui suivent et celles qui suivent ce bloc de code pour
            # changer le template de détail d'un livre
            //    ->overrideTemplates([
            //     'crud/detail' => 'admin/book/show.html.twig',
            // ])

        ;
        
    }

    // //cette méthode permet de changer le template de détail d'un livre
    // public function detail(AdminContext $context): Response
    // {
    //     //récupérer le livre en question
    //     $book = $context->getEntity()->getInstance();

    //     return $this->render('admin/book/show.html.twig', [
    //         'book' => $book,
    //     ]);
    // }
    
    public function configureFields(string $pageName): iterable
    {
        return [

            IdField::new("id")
            ->onlyOnIndex()
            ,

            TextField::new('isbn', "N° isbn")
            ,

            TextField::new('title', "titre")
            ,

            ChoiceField::new("status")
            ->setChoices([
                BookStatus::Available->getLabel() => BookStatus::Available,
                BookStatus::Borrowed->getLabel() => BookStatus::Borrowed,
                BookStatus::Unavailable->getLabel() => BookStatus::Unavailable,
            ])
            ->formatValue(function ($value) {
                return $value->getLabel();
            }),

            AssociationField::new("editor", "Editeur")
            ->hideOnIndex()
            ,

            AssociationField::new("authors", "Auteur(s)")
            ->hideOnIndex()
            ,

            TextareaField::new('plot', "Resumé")
            ->hideOnIndex()
            ,
            
            IntegerField::new('pageNumber', "Nombre de page")
            ,

            AssociationField::new("createdBy", "Crée par")
            ->hideOnForm()
            ,

        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)  //ajouter la page de consulation (détails) d'un livre
        ;
    }
    
}
