<?php

namespace App\Form\UserAccounts;


use App\Entity\UserAccounts\Permission;
use App\Form\EventListener\AddPermissionDataRolePermissionForm;
use App\Service\FileLoader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolePermissionFormType extends  AbstractType
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FileLoader
     */
    private $fileLoader;
    /**
     * @var
     */
    private $permissionsFile;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManagerInterface $entityManager, FileLoader $fileLoader,RequestStack $requestStack,String $permissionsFile)
    {
        $this->em = $entityManager;
        $this->fileLoader = $fileLoader;
        $this->permissionsFile = $permissionsFile;
        $this->requestStack = $requestStack;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('role',RoleFormType::class)
            ->addEventSubscriber(new AddPermissionDataRolePermissionForm($this->em,
                $this->fileLoader,$this->requestStack,$this->permissionsFile));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>Permission::class
        ]);
    }


}