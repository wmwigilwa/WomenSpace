<?php

namespace App\Controller\Forum;

use App\Entity\Forum\Reply;
use App\Entity\Forum\Topic;
use App\Form\Forum\ReplyType;
use App\Form\Forum\TopicType;
use App\Repository\Configuration\SpaceRepository;
use App\Repository\Forum\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum/topic')]
class TopicController extends AbstractController
{
    #[Route('/', name: 'app_forum_topic_index', methods: ['GET'])]
    public function index(Request $request, TopicRepository $topicRepository, SpaceRepository $spaceRepository): Response
    {
        $space = $spaceRepository->findOneBy(['id'=>$request->get('space')]);

        return $this->render('forum/topics.html.twig', [
            'topics' => $topicRepository->findBySpace($space),
            'space' => $space,
            'page_name'=>'Topics'
        ]);
    }

    #[Route('/new', name: 'app_forum_topic_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SpaceRepository $spaceRepository): Response
    {
        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        $space = $spaceRepository->findOneBy(['id'=>$request->get('space')]);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setOwner($this->getUser());
            $topic->setSpace($space);
            $topic->setDateCreated(new \DateTimeImmutable());
            $entityManager->persist($topic);
            $entityManager->flush();
            return $this->redirectToRoute('app_forum_topic_index', ['space'=>$space->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum/topics.form.html.twig', [
            'topic' => $topic,
            'form' => $form,
            'formTitle'=>$space->getTitle(),
            'page_name'=>'Create new Topic'
        ]);
    }

    #[Route('/{id}', name: 'app_forum_topic_show', methods: ['GET','POST'])]
    public function show(Request $request, EntityManagerInterface $entityManager, Topic $topic): Response
    {

        $reply = new Reply();

        $form = $this->createForm(ReplyType::class, $reply);
        $form->handleRequest($request);

        $space = $topic->getSpace();

        if ($form->isSubmitted() && $form->isValid()) {
            $reply->setOwner($this->getUser());
            $reply->setTopic($topic);
            $reply->setDateCreated(new \DateTimeImmutable());
            $entityManager->persist($reply);
            $entityManager->flush();
            return $this->redirectToRoute('app_forum_topic_show', ['id'=>$topic->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum/topics.show.html.twig', [
            'topic' => $topic,
            'space' => $topic->getSpace(),
            'form' => $form,
            'page_name'=>'View Topic'
        ]);
    }


}
