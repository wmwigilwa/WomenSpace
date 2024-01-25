<?php

namespace App\Controller;

use App\Entity\UserAccounts\User;
use App\Form\UserAccounts\SignUpType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'page_name' => 'Home'
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/main.form.html.twig', [
            'controller_name' => 'HomeController',
            'page_name' => 'Login',
            'form_template'=>'home/form/login.html.twig',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/sign-up', name: 'app_sign_up')]
    public function signUp(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {

        $form = $this->createForm(SignUpType::class, null);


        if ($request->getMethod() === "POST") {

            $fullName =  $request->get('_full_name');
            $email =  $request->get('_username');
            $password =  $request->get('_password');

            if ($fullName != null && $email != null && $password != null) {
                $fullName = explode(' ', $fullName);
                $user = new User();
                $user->setEmail($email);
                $user->setAccountStatus('A');
                $user->setFirstName($fullName[0]);
                $user->setPassword($hasher->hashPassword($user,$password));

                if (count($fullName) > 2) {
                    $user->setMiddleName($fullName[1]);
                    $user->setLastName($fullName[2]);
                } else if (count($fullName) == 2) {
                    $user->setLastName($fullName[1]);
                    $user->setMiddleName("");
                }

                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Your account has been successfully created, you may login to your account');
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('home/main.form.html.twig', [
            'controller_name' => 'HomeController',
            'page_name' => 'Sign up',
            'form_template'=>'home/form/sign.up.html.twig',
            'error'=>'',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new Exception('logout() should never be reached.');
    }
}
