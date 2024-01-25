<?php

namespace App\Controller\UserAccounts;

use App\Form\UserAccounts\ResetPasswordFormType;
use App\Repository\UserAccounts\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @Route("/app/user-account/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/change-my-password", name="change_account_password")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    public function changePasswordAction(Request $request,
                                         UserRepository $userRepository,
                                        UserPasswordHasherInterface $passwordHasher)
    {
        $user = $this->getUser();

        $form = $this->createForm(ResetPasswordFormType::class);

        // only handles data on POST
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $password = $data->getPassword();

            if ($passwordHasher->isPasswordValid($user, $password))
            {

                $this->addFlash('error', 'You can not use your current password as the new password');

                return $this->redirectToRoute('change_account_password');
            }

            if (!$passwordHasher->isPasswordValid($user, $data->getPlainPassword()))
            {
                $this->addFlash('error', 'Please enter your correct current password');

                return $this->redirectToRoute('change_account_password');
            }

            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $userRepository->add($user,true);

            $this->addFlash('success', 'Password successfully changed');

            return $this->redirectToRoute('app_home');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'user_accounts/user/reset.password.html.twig',
                'form'=>$form->createView(),
                'title'=>'Password change form'
            )

        );
    }
    
}