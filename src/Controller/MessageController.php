<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    private $manager;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();   
    }
    
    #[Route("/messages", name:"message_list")]
    public function listMessages(): Response
    {
        $user = $this->getUser();
        $receivedMessages = $this->manager->getRepository(Chat::class)->findByReceiver($user);
        $sentMessages = $this->manager->getRepository(Chat::class)->findBySender($user);
        $messageNonLu = $this->manager->getRepository(Chat::class)->findMessageNonLu($user);

        foreach ($receivedMessages as $message){
            if (!$message->isIsRead()){
                $message->setIsRead(true);
            }
        }

        $this->manager->flush();


        return $this->render('message/index.html.twig', [
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages,
            'messageNonLu' => $messageNonLu,
        ]);
    }

    #[Route("/messages/{id}", name:"message_conversation")]
    public function viewConversation(User $otherUser): Response
    {
        $user = $this->getUser();
        $messages = $this->manager->getRepository(Chat::class)->findConversation($user, $otherUser);

        return $this->render('message/conversation.html.twig', [
            'otherUser' => $otherUser,
            'messages' => $messages,
        ]);
    }

    
    #[Route("/messages/send/{id}", name:"message_send")]
    public function sendMessage(User $receiver, Request $request): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');

            #$entityManager = $this->manager->getManager();

            $message = new Chat();
            $message->setSender($user);
            $message->setReceiver($receiver);
            $message->setContent($content);
            $message->setIsRead(false);
            $message->setTimestamp(new \DateTime());

            $this->manager->persist($message);
            $this->manager->flush();
        }

        return $this->redirectToRoute('message_conversation', ['id' => $receiver->getId()]);
    }
}

