<?php

namespace App\Controller\UserAccounts;

use App\Entity\UserAccounts\User;
use App\Form\UserAccounts\UserType;
use App\Repository\UserAccounts\RoleRepository;
use App\Repository\UserAccounts\UserRepository;
use App\Service\GridBuilder;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Pagerfanta\Doctrine\DBAL\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/app/user-accounts/users')]
class UserController extends AbstractController
{

    #[Route('/', name: 'app_user_accounts_user_index', methods: ['GET'])]
    public function index(GridBuilder $grid, UserRepository $em, Request $request): Response
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['name'] = $request->query->get('name');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $qb1 = $em->getAll($options);

        $qb2 = $em->countAll($qb1);

        $adapter = new QueryAdapter($qb1,$qb2);

        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Username','username','text',true);
        $grid->addGridHeader('Full Name','fullName','text',true);
        $grid->addGridHeader('Role','role','text',true);
        $grid->addGridHeader('Account Status',null,'text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('app_user_accounts_user_index');
        $grid->setCurrentObject($class);
        $grid->setButtons();

        //Render the output
        return $this->render('main/app.list.html.twig',array(
            'records'=>$dataGrid,
            'grid'=>$grid,
            'page_name'=>'Existing User Accounts',
            'gridTemplate'=>'user_accounts/user/index.html.twig'
        ));
    }

    #[Route('/new', name: 'app_user_accounts_user_new', methods: ['GET','POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setAccountStatus('A');
            $userRepository->add($user, true);

            $this->addFlash('success', 'User account successfully created');
            return $this->redirectToRoute('app_user_accounts_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'user_accounts/user/form.add.html.twig',
                'form'=>$form->createView(),
                'isFullWidth'=>true,
                'isAdd'=>true,
                'page_name'=>'Create a new User'
            )
        );
    }


    #[Route('/{id}/show', name: 'app_user_accounts_user_show', defaults: ['id' => 0], methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('main/app.show.html.twig', [
            'user' => $user,
            'page_name'=>'User Account Details',
            'template'=>'user_accounts/user/show.html.twig'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_accounts_user_edit', defaults: ['id'=>0], methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);
            $this->addFlash('success', 'User account successfully updated');
            return $this->redirectToRoute('app_user_accounts_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'user_accounts/user/form.edit.html.twig',
                'form'=>$form->createView(),
                'isFullWidth'=>true,
                'isAdd'=>true,
                'page_name'=>'Edit existing User'
            )

        );
    }

    #[Route('/{id}/delete', name: 'app_user_accounts_user_delete', methods: ['GET'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        try
        {
            $userRepository->remove($user, true);
            $this->addFlash('success', 'User account successfully removed');
        }
        catch (ForeignKeyConstraintViolationException $e)
        {
            $this->addFlash('error', 'Unable to delete user account, this account has other records associated with it');
        }

        return $this->redirectToRoute('app_user_accounts_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/activate', name: 'app_user_activate', methods: ['GET'])]
    public function activate(Request $request, User $user,
                             UserRepository $userRepository,
                             RoleRepository $roleRepository,
                             UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $class = get_class($this);
        $this->denyAccessUnlessGranted('approve',$class);
        $user->setAccountStatus('A');
        $userRepository->add($user, true);
        $this->addFlash('success', 'User account has been successfully activated !');

        return $this->redirectToRoute('app_user_accounts_user_show', ['id'=>$user->getId()],
            Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/deactivate', name: 'app_user_deactivate', methods: ['GET'])]
    public function deactivate(Request $request,
                               User $user,
                               UserPasswordHasherInterface $passwordHasher,
                               UserRepository $userRepository): Response
    {
        $class = get_class($this);
        $this->denyAccessUnlessGranted('decline',$class);

        $user->setAccountStatus('B');
        $userRepository->add($user,true);

        $this->addFlash('success', 'User account has been successfully deactivated !');

        return $this->redirectToRoute('app_user_accounts_user_show', ['id'=>$user->getId()],
            Response::HTTP_SEE_OTHER);
    }

}
