<?php

namespace App\Form\Location;

use App\Entity\Location\Country;
use App\Entity\Location\SubRegion;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('subRegion', EntityType::class, [
                'class' => SubRegion::class,
                'choice_label' => 'subRegionLabel',
                'placeholder'=>'Choose a Sub-Region',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('sr')
                        ->join('sr.region', 'r')
                        ->addOrderBy('r.name', 'ASC')
                        ->addOrderBy('sr.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
