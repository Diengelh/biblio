<?php

namespace App\Form;
use App\Entity\Mark;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', IntegerType::class, [
                "attr" => [
                    "class" => "mt-4",
                    "placeholder" => "Notez ce livre s'il vous plaît",
                    "min" => "0",
                    "max" => "5",
                ],
                "label" => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mark::class,
        ]);
    }
}
