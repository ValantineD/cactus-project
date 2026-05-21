<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account/messagerie', name: 'account_')]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController  // ← was missing
{
    #[Route('/{username}', name: 'conversation')]   // ← plain {username} param
    public function conversation(
        string                 $username,           // ← receive it as a string
        UserRepository         $userRepository,
        MessageRepository      $messageRepository,
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $currentUser = $this->getUser();
        $otherUser = $userRepository->findOneBy(['username' => $username]); // ← find by username

        if (!$otherUser || $otherUser === $currentUser) {
            throw $this->createNotFoundException();
        }

        if ($request->isMethod('POST')) {
            $content = trim($request->request->get('content', ''));
            if ($content !== '') {
                $message = new Message();
                $message->setSender($currentUser);
                $message->setReceiver($otherUser);
                $message->setContent($content);
                $message->setSendingDate(new \DateTime());
                $entityManager->persist($message);
                $entityManager->flush();
            }
            // ← redirect using 'username', matching the route param above
            return $this->redirectToRoute('account_conversation', ['username' => $username]);
        }

        $messages = $messageRepository->findConversation($currentUser, $otherUser);

        return $this->render('account/conversation.html.twig', [
            'otherUser' => $otherUser,
            'messages'  => $messages,
        ]);
    }
}
