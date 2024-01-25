<?php

namespace App\Form\Location;

use App\Entity\Location\Region;
use App\Entity\Location\SubRegion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubRegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'name',
                'placeholder' => 'Select region'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubRegion::class,
        ]);
    }
}
