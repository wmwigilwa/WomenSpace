<?php

namespace App\Form\CustomField;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


use Symfony\Component\Form\AbstractType;

class GenderType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                '--- Select a value ---'=>null,
                'Male' => 'M',
                'Female' => 'F',
            )
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }


}