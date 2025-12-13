<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

//AbstractType est la classe qui implémente FormTypeInteface
class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
            ])
            ->add('dateOfDeath', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'required' => false,
            ])
            ->add('nationality', TextType::class, [
                'required' => false,
            ])
            ->add('books', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                "by_reference" => false, //doit être ajouté dans le cadre d'une relation ManyToMany
            ])
            ->add("certification", CheckboxType::class, [
                "mapped" => false, // doit être ajouté si le champ n'appartient pas à l'entité à remplir
                "constraints" => [
                    new Assert\IsTrue(message: "vous devez cochez la case pour ajouter un livre")
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
