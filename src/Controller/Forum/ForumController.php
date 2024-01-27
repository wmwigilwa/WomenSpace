<?php

namespace App\Controller\Forum;

use App\Form\Configuration\SpaceType;
use App\Repository\Configuration\SpaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/app/forum')]
class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum_home')]
    public function index(SpaceRepository $spaceRepository): Response
    {

        $spaces = $spaceRepository->findAll();

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'HomeController',
            'spaces' => $spaces,
            'page_name' => 'Forums'
        ]);
    }

}
