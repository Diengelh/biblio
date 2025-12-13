<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add("firstname", TextType::class)
            ->add("lastname", TextType::class)
            ->add("username", TextType::class,[
                "required" => false
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur-rice' => 'ROLE_USER',
                    'Modérateur' => 'ROLE_MODERATEUR',
                    'Ajouteur de livre' => 'ROLE_AJOUT_DE_LIVRE',
                    'Editeur de livre' => 'ROLE_EDITION_DE_LIVRE',
                    'Administrateur-rice' => 'ROLE_ADMIN',
                ],
                "multiple" => true,
                "expanded" => true, //afficher sous forme de liste de case à cocher
            ])
            ->add('plainPassword', PasswordType::class, [
                // au lieu de rattacher le mot de passe directement à l'objet,
                // le mot de passe sera lu et encoder dans le controller.
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe s\'il vous plaît',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Mot mot de passe doit avoir au minimum 6 caractères de long',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
