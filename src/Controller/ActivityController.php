<?php


namespace App\Controller;


use App\Entity\Activity;
use App\Form\ActivityFormType;
use App\Repository\ActivityRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $activity = new Activity();
//        $activity->setStatus("draft");

        $form = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $activity->setUser($user);

            $pictureFile = $form->get('images')->getData();
            if ($pictureFile) {
                $context =   "activity";
//                $newFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $fileUploader->uploadImage($pictureFile, $context);

                $activity->setImages('uploads/activities/' . $newFilename);
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
        ]);
    }


    #[Route('/{id}/edit', name: 'app_activity_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Activity $activity,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response
    {
        $form = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if ($this->getUser() !== $activity->getUser()) {
            throw new AccessDeniedHttpException('You cannot edit this activity because you are not its creator!');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('images')->getData();
            if ($pictureFile) {
                if ($activity->getImages()) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . $activity->getImages();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $context = "activity";
                $newFilename = $fileUploader->uploadImage($pictureFile, $context);
                $activity->setImages('uploads/activities/' . $newFilename);
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
