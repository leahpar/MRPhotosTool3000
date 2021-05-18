<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateSemaineFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'À venir' => 0,
                'Passées' => 1,
                'toutes'  => 2,
            ],
            'expanded' => true,
            'data' => 0,
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}