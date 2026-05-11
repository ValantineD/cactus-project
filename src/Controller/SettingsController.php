<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/uploads/user/profile')] string $profileDirectory
    ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            $oldFilename = $user->getProfileFilename();

            if ($pictureFile) {
                $mimeType    = $pictureFile->getMimeType();
                $extension   = explode('/', $mimeType)[1];
                $realFilename = uniqid('profile_' . $user->getId() . '_') . '.' . $extension;

                try {
                    $pictureFile->move($profileDirectory, $realFilename);
                } catch (FileException $e) {
                    throw new \Exception($e->getMessage());
                }


                $filePath    = $profileDirectory . '/' . $realFilename;
                $imageCreate = 'imagecreatefrom' . $extension;

                /** @var \GdImage $uploadImage */
                $uploadImage  = $imageCreate($filePath);
                $resizedImage = $this->resizeProfileImage($uploadImage, 200, 200);

                $saveImage = 'image' . $extension;
                $saveImage($resizedImage, $filePath);

                imagedestroy($uploadImage);
                imagedestroy($resizedImage);

                $user->setProfileFilename('uploads/user/profile/' . $realFilename);

                if ($oldFilename && str_starts_with($oldFilename, 'uploads/user/profile/')) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . $oldFilename;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }


            $selectedProfile = $form->get('profileFilename')->getData();
            if ($selectedProfile && str_starts_with($selectedProfile, 'images/profiles/')) {
                $oldFilename = $user->getProfileFilename();
                if ($oldFilename && str_starts_with($oldFilename, 'uploads/user/profile/')) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . $oldFilename;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', [
                'username' => $user->getUsername(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    private function resizeProfileImage(\GdImage $uploadImage, int $width, int $height): \GdImage
    {
        $oldw = imagesx($uploadImage);
        $oldh = imagesy($uploadImage);

        $square = min($oldw, $oldh);
        $x = (int)(($oldw - $square) / 2);
        $y = (int)(($oldh - $square) / 2);

        $temp = imagecreatetruecolor($width, $height);
        imagecopyresampled($temp, $uploadImage, 0, 0, $x, $y, $width, $height, $square, $square);
        return $temp;
    }

}
