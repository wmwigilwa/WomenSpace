<?php


namespace App\Form\EventListener;

use App\Entity\UserAccounts\Role;
use App\Form\UserAccounts\RoleFormType;
use App\Form\CustomField\IgnoreChoiceType;
use App\Service\FileLoader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class AddPermissionDataRolePermissionForm implements EventSubscriberInterface
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
     * @var string
     */
    private string $permissionsFile;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManagerInterface $em,FileLoader $fileLoader,RequestStack $requestStack,String $permissionsFile)
    {
        $this->em = $em;
        $this->fileLoader = $fileLoader;
        $this->permissionsFile = $permissionsFile;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {

        $form = $event->getForm();

        $data = $this->fileLoader->loadFile($this->permissionsFile);

        $roleId = $this->requestStack->getCurrentRequest()->get('roleId');

        $selectedPermissions = new ArrayCollection();

        if($roleId != null)
        {
            $role = $this->em->getRepository(Role::class)
                ->findOneBy(['id' => $roleId]);

            $availablePermissions = $permissions=$role->getPermissions();

            foreach ($availablePermissions as $availablePermission)
            {
                $selectedPermissions->set($availablePermission->getObject(),$availablePermission->getActions());
            }

            $form->add('role',RoleFormType::class,['data'=>$role]);
        }


        if($data!=null)
        {
            foreach ($data as $menu)
            {
                foreach ($menu as $item)
                {
                    $actionChoices = array();

                    foreach($item['actions'] as $action)
                    {
                        $actionChoices[] = [ucfirst($action) => ($action)];
                    }

                    if($selectedPermissions !=null)
                    {
                        $data = $selectedPermissions->get($item['roleClass']);
                    }


                    $form->add($item['key'], IgnoreChoiceType::class, array(
                        'multiple'=>true,
                        'expanded'=>true,
                        'attr'=>['class'=>'flex gap-x-6 flex-wrap'],
                        'label'=>ucwords(str_replace('-',' ',$item['key'])),
                        'choices'  => $actionChoices,
                        'data'=>$data
                    ));
                }
            }

        }

    }
}