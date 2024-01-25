<?php

namespace App\Form\CustomField;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IgnoreChoiceType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'mapped' => false,
            'required' => false
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->resetViewTransformers();
    }


    public function getParent()
    {
        return ChoiceType::class;
    }




    
}