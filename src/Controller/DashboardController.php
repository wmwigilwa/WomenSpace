<?php

namespace App\Controller;

use App\Repository\Data\CountryFactRepository;
use App\Repository\Data\QuizRepository;
use App\Repository\Location\CountryRepository;
use App\Repository\UserAccounts\UserRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/app/dashboard', name: 'app_dashboard')]
    public function index(UserRepository $userRepository,
                          CountryRepository $countryRepository,
                          QuizRepository $quizRepository,
                          CountryFactRepository $countryFactRepository

    ): Response
    {
        $totals = [
            'users' => $userRepository->getTotals(),
            'countries' => $countryRepository->getTotals(),
            'quizzes' => $quizRepository->getTotals(),
            'facts' => $countryFactRepository->getTotals(),
        ];

        $leaderBoards = $quizRepository->getCurrentLeaderBoards();


        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'page_name'=>'Dashboard',
            'leaderBoards'=>$leaderBoards,
            'totals'=>$totals
        ]);
    }

}
