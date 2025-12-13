<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\BookStatus;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class)
            ->add('isbn', TextType::class, [
                "attr" => [
                    "placeholder" => "13 chiffres"
                ]
            ])
            ->add('cover', Texttype::class, [
                "attr" => [
                    "placeholder" => "https:://exemple@..."
                ]
            ])
            ->add('imageFile', VichFileType::class, [
                'required' => false,
            ])

            ->add('editedAt', DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
            ])
            ->add('plot', TextareaType::class)
            ->add('pageNumber', IntegerType::class, [
                'attr' => ["min" => "1"]
            ])
            ->add('status', EnumType::class, [
                'class' => BookStatus::class
            ])
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                // Pas besoin d'ajouter 'by_reference' => false car la relation ManyToMany
                // entre Book et Author a été définie côté Book
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
