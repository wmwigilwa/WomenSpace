<?php

namespace App\Form\Forum;

use App\Entity\Configuration\Space;
use App\Entity\Configuration\Tag;
use App\Entity\Forum\Topic;
use App\Entity\UserAccounts\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'description',
                'multiple' => true,
                'expanded'=>true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
}
