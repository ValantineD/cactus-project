<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\ImageFile;
use App\Entity\Theme;
use App\Form\ActivityFormType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/activity')]
final class ActivityController extends AbstractController
{
    #[Route('', name: 'app_activity_index', methods: ['GET'])]
    public function index(ActivityRepository $activityRepository): Response
    {
        return $this->render('activity/index.html.twig', [
            'activities' => $activityRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_activity_new', methods: ['GET', 'POST'])]
    public function new(
        Request                                                              $request,
        EntityManagerInterface                                               $entityManager,
        #[Autowire('%kernel.project_dir%/public/uploads/activities')] string $imageActivityDirectory
    ): Response
    {
        $activity = new Activity();

        $form = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activity->setUser($this->getUser());

            foreach ($form->get('imageFiles') as $index => $file) {
                $uploadedFile = $file->get('file')->getData();

                if ($uploadedFile) {
                    $position = $index + 1;
                    $mimeType = $uploadedFile->getMimeType();
                    $extension = explode('/', $mimeType)[1];
                    $newFilename = uniqid('image_file_activity_' . $position . '_') . '.' . $extension;

                    try {
                        $uploadedFile->move($imageActivityDirectory, $newFilename);
                    } catch (FileException $e) {
                        throw new Exception($e->getMessage());
                    }

                    $imageFile = null;
                    foreach ($activity->getImageFiles() as $existingImage) {
                        if ($existingImage->getPosition() === $position) {
                            $imageFile = $existingImage;
                            break;
                        }
                    }

                    if (!$imageFile) {
                        $imageFile = new ImageFile();
                        $activity->addImageFile($imageFile);
                    }

                    $imageFile->setFilename('uploads/activities/' . $newFilename);
                    $imageFile->setPosition($position);
                    $entityManager->persist($imageFile);
                }

                foreach ($activity->getThemes() as $theme) {
                    if (!$theme->getActivities()->contains($activity)) {
                        $theme->addActivity($activity);
                    }
                }

                foreach ($entityManager->getRepository(Theme::class)->findAll() as $theme) {
                    if (!$activity->getThemes()->contains($theme)) {
                        $theme->getActivities()->removeElement($activity);
                    }
                }
            }

            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_show', methods: ['GET'])]
    public function show(Activity $activity): Response
    {
        return $this->render('activity/show.html.twig', [
            'activity' => $activity,
            'imageFiles' => $activity->getImageFiles()
        ]);
    }


    #[Route('/{id}/edit', name: 'app_activity_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        Activity               $activity,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/uploads/activities')] string $imageActivityDirectory
    ): Response
    {
        if ($this->getUser() !== $activity->getUser()) {
            throw new AccessDeniedHttpException('You cannot edit this activity because you are not its creator!');
        }

        $form = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $deleteIds = $request->request->all('deleteImages');
            foreach ($deleteIds as $deleteId) {
                foreach ($activity->getImageFiles() as $imageFile) {
                    if ($imageFile->getId() === (int) $deleteId) {
                        $activity->removeImageFile($imageFile);
                        $entityManager->remove($imageFile);
                        break;
                    }
                }
            }

            $entityManager->flush();

            $position = 1;
            foreach ($activity->getImageFiles() as $imageFile) {
                $imageFile->setPosition($position++);
                $entityManager->persist($imageFile);
            }

            $existingCount = $activity->getImageFiles()->count();
            foreach ($form->get('imageFiles') as $index => $file) {
                $uploadedFile = $file->get('file')->getData();

                if ($uploadedFile) {
                    $position = $existingCount + $index + 1;
                    $mimeType = $uploadedFile->getMimeType();
                    $extension = explode('/', $mimeType)[1];
                    $newFilename = uniqid('image_file_activity_' . $position . '_') . '.' . $extension;

                    try {
                        $uploadedFile->move($imageActivityDirectory, $newFilename);
                    } catch (FileException $e) {
                        throw new \Exception($e->getMessage());
                    }

                    $imageFile = new ImageFile();
                    $imageFile->setFilename('uploads/activities/' . $newFilename);
                    $imageFile->setPosition($position);
                    $activity->addImageFile($imageFile);
                    $entityManager->persist($imageFile);
                }
            }

            foreach ($activity->getThemes() as $theme) {
                if (!$theme->getActivities()->contains($activity)) {
                    $theme->addActivity($activity);
                }
            }

            foreach ($entityManager->getRepository(Theme::class)->findAll() as $theme) {
                if (!$activity->getThemes()->contains($theme)) {
                    $theme->getActivities()->removeElement($activity);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/delete', name: 'app_activity_delete', methods: ['POST'])]
    public function delete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $activity->getUser()) {
            throw new AccessDeniedHttpException('You cannot delete this activity because you are not its creator!');
        }

        if ($this->isCsrfTokenValid('delete' . $activity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
    }


}
