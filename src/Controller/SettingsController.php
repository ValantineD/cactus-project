<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
final class SettingsController extends AbstractController
{
    #[Route('/', name: 'app_user_parameters')]
    public function parameters(): Response
    {
        return $this->render('settings/parameters.html.twig');
    }

    #[Route("/profile", name: "app_user_edit", methods: ["GET", "POST"])]
    public function edit(
        Request                                                                $request,
        EntityManagerInterface                                                 $entityManager,
        FileUploader $fileUploader
    ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('picture')->getData();

            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile) {
                $oldFilename = $user->getProfileFilename();
                if ($oldFilename && str_starts_with($oldFilename, 'uploads/user/profile/')) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . $oldFilename;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $newFilename = $fileUploader->uploadImage($pictureFile, "user");
                $user->setProfileFilename('uploads/user/profile/' . $newFilename);
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

}
