<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('account/profile/messagerie', name: 'app_', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{

    #[Route('', name: 'messagerie')]
    public function messagerie(
        Request           $request,
        MessageRepository $messageRepository,
        UserRepository    $userRepository
    ): Response
    {
        $currentUser = $this->getUser();
        $messages = $messageRepository->messagerie($currentUser);

        $conversations = [];
        foreach ($messages as $message) {
            $otherUser = $message->getSender() === $currentUser
                ? $message->getReceiver()
                : $message->getSender();

            $otherUserId = $otherUser->getId();

            if (!isset($conversations[$otherUserId])) {
                $conversations[$otherUserId] = [
                    'user' => $otherUser,
                    'lastMessage' => $message,
                ];
            }
        }

        $q = $request->query->get('q', '');
        $users = $q ? $userRepository->createQueryBuilder('u')
            ->where('u.username LIKE :q')
            ->setParameter('q', "%$q%")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult() : [];

        return $this->render('account/messagerie.html.twig', [
            'conversations' => $conversations,
            'q' => $q,
            'users' => $users,
        ]);
    }


    #[Route('/{username}', name: 'conversation', methods: ['GET', 'POST'])]
    public function conversation(
        string                 $username,
        UserRepository         $userRepository,
        MessageRepository      $messageRepository,
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $currentUser = $this->getUser();
        $otherUser = $userRepository->findOneBy(['username' => $username]);

        if (!$otherUser) {
            throw $this->createNotFoundException();
        }

        if ($otherUser === $currentUser) {
            return $this->redirectToRoute('app_user_show', ['username' => $currentUser->getUsername()]);
        }


        if ($request->isMethod('POST')) {
            $content = trim($request->request->get('content', ''));
            if ($content !== '') {
                $message = new Message();
                $message->setSender($currentUser);
                $message->setReceiver($otherUser);
                $message->setContent($content);
                $entityManager->persist($message);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_conversation', ['username' => $username]);
        }

        $messages = $messageRepository->findConversation($currentUser, $otherUser);

        return $this->redirectToRoute('app_conversation', ['username' => $username]);
    }


}
