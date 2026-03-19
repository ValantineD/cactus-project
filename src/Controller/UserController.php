<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/account")]
class UserController extends AbstractController
{
    #[Route('/{username:user}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('account/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route("/{username:user}/edit", name: "app_user_edit", methods: ["GET", "POST"])]
    public function edit(
        Request                                                                $request,
        User                                                                   $user,
        EntityManagerInterface                                                 $entityManager,
        #[Autowire('%kernel.project_dir%/public/uploads/user/profile')] string $pictureDirectory
    ): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile) {
                // remove old image
                if ($user->getProfileFilename()) {
                    $oldImagePath = $this->getParameter('kernel.project_dir') . '/uploads/user/profile/' . $user->getProfileFilename();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $newFilename = "uploads/user/profile/". uniqid('user_', true) . '.' . $pictureFile->guessExtension();

                try {
                    $pictureFile->move($pictureDirectory, $newFilename);
                } catch (FileException $e) {
                    throw new Exception($e->getMessage());
                }

                $user->setProfileFilename($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', [
                'username' => $user->getUsername(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

}
