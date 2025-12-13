<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class)
            ->add("new_password", RepeatedType::class, [
                "first_options" => [
                    "label" => "Nouveau mot de passe",
                ],
                "second_options" => [
                    "label"  => "confirmation de mot de passe",
                ],
                "invalid_message" => "Les deux mots de passe ne correspondent pas",
                "constraints" => [
                    new Assert\PasswordStrength(
                        minScore: 1,
                    )
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
