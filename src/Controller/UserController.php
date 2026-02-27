<?php

namespace App\Controller;

use App\Entity\User;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    #[Route('/', name: 'cactus_db_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'title' => 'Voici ma page index user',
            'users' => $users
        ]);

    }
    #[Route('/new', name: "cactus_db_user_new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('email')
            ->add('password')
            ->add('submit', SubmitType::class,[
                'label' => 'Enregistrer'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            echo('Form submitted!');

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('cactus_db_user_index');
        }

        return $this->render('/user/new.html.twig', [
            'form' => $form,
        ]);
    }
}
