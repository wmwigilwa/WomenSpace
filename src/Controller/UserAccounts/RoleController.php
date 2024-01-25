<?php

namespace App\Controller\UserAccounts;

use App\Entity\UserAccounts\Permission;
use App\Entity\UserAccounts\Role;
use App\Form\UserAccounts\RolePermissionFormType;
use App\Repository\UserAccounts\PermissionRepository;
use App\Repository\UserAccounts\RoleRepository;
use App\Service\FileLoader;
use App\Service\GridBuilder;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\DBAL\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/app/role')]
class RoleController extends AbstractController
{

    #[Route('/', name: 'app_role_index', methods: ['GET'])]
    public function index(GridBuilder $grid, RoleRepository $em, Request $request): Response
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['name'] = $request->query->get('name');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $this->addFlash('warning','Editing system default roles may render system unusable');

        $qb1 = $em->findAllRoles($options);

        $qb2 = $em->countAllRoles($qb1);

        $adapter =new QueryAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Role Name','name','text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('app_role_index');
        $grid->setCurrentObject($class);
        $grid->setButtons();
    
        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'page_name'=>'Existing Roles',
                'gridTemplate'=>'main/base.list.html.twig'
             ));
    }

    #[Route('/new', name: 'app_role_new', methods: ['GET','POST'])]
    public function new(Request $request, FileLoader $loader, RoleRepository $em,
                        PermissionRepository $permissionRepository): Response
    {
        $this->denyAccessUnlessGranted('add',get_class($this));
        
        $form = $this->createForm(RolePermissionFormType::class);

        $permissions = $loader->loadFile($this->getParameter('permissions_file'));
        
        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data =  $form->getData();
            $role = $data->getRole();
            $em->add($role, true);

            $roleId = $role->getId();

            $actionsSelected =$loader
                ->loadFile($this->getParameter('permissions_file'));

            foreach ($actionsSelected as $menu)
            {
                foreach ($menu as $item)
                {
                    $actionsSelected = $form[$item['key']]->getData();

                    if(!empty($actionsSelected))
                    {
                        $actionsSelected = json_encode($actionsSelected);

                        $object = $item['roleClass'];
                        $permissionRepository
                            ->recordPermission($object, $roleId, $actionsSelected);
                    }
                }
            }
            
            $this->addFlash('success', 'Role successfully created');
            
            return $this->redirectToRoute('app_role_index');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'user_accounts/roles/form.role.html.twig',
                'permissions'=>$permissions,
                'form'=>$form->createView(),
                'isFullWidth'=>true,
                'page_name'=>'Role Details'
            )

        );
    }

    /**
     * @throws Exception
     */
    #[Route('/{roleId}/edit', name: 'app_role_edit', defaults: ['roleId'=>0], methods: ['GET','POST'])]
    public function edit(FileLoader $fileLoader,Request $request,EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('edit',get_class($this));

        $form = $this->createForm(RolePermissionFormType::class);

        $form->handleRequest($request);

        $permissions = $fileLoader
            ->loadFile($this->getParameter('permissions_file'));

        if ($form->isSubmitted() && $form->isValid())
        {
            $data =  $form->getData();

            $role = $data->getRole();

            $em->persist($role);
            $em->flush();

            $roleId = $role->getId();

            $actionsSelected =$fileLoader
                ->loadFile($this->getParameter('permissions_file'));

            $em->getRepository(Permission::class)
                ->clearPermissionByRoleId($roleId);

            foreach ($actionsSelected as $menu)
            {
                foreach ($menu as $item)
                {
                    $actionsSelected = $form[$item['key']]->getData();

                    if(!empty($actionsSelected))
                    {
                        $actionsSelected = json_encode($actionsSelected);

                        $object = $item['roleClass'];
                        $em->getRepository(Permission::class)
                            ->recordPermission($object, $roleId, $actionsSelected);
                    }
                }
            }

            $this->addFlash('success', 'Role successfully updated');

            return $this->redirectToRoute('app_role_index');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'user_accounts/roles/form.role.html.twig',
                'form'=>$form->createView(),
                'permissions'=>$permissions,
                'isFullWidth'=>true,
                'page_name'=>'Role Details'
            )

        );
    }

    #[Route('/{roleId}/delete', name: 'app_role_delete', defaults: ['roleId'=>0], methods: ['GET'])]
    public function deleteAction(EntityManagerInterface $em, $roleId): Response
    {
        $this->denyAccessUnlessGranted('delete',get_class($this));

        $role = $em->getRepository(Role::class)->find($roleId);

        if($role instanceof Role)
        {
            $em->remove($role);
            $em->flush();
            $this->addFlash('success', 'Role successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Role not found !');
        }

        return $this->redirectToRoute('app_role_index');
    }

}