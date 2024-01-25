<?php

namespace App\Form\UserAccounts;

use App\Entity\UserAccounts\Role;
use App\Entity\UserAccounts\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack){

        $this->requestStack = $requestStack;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',TextType::class,[
                'constraints'=>[
                    new NotBlank()
                ]
            ])
            ->add('userRoles', EntityType::class, [
                'class' => Role::class,
                'placeholder' => 'Select role',
                'label'=>'User roles',
                'multiple'=>true,
                'expanded'=>true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.roleName', 'ASC');
                },
                'required'=>false,
                'choice_label' => 'roleName'
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('middleName');

        if(str_contains($this->requestStack->getCurrentRequest()->getUri(), 'new')) {
            $builder->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeated Password'),
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
