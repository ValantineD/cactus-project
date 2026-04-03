<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity')]
final class ActivityController extends AbstractController
{
    #[Route('/', name: 'app_activity')]
    public function index(): Response
    {
        return $this->render('activity/index.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }

    #[Route('/new', name: 'app_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%kernel.project_dir%/public/uploads/activities')] string $pictureDirectory): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $activity->setUser($user);
//
//            $pictureFile = $form->get('images')->getData();
//            if ($pictureFile) {
//                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
//
//                if ($activity->getImages()) {
//                    $oldImagePath = $this->getParameter('kernel.project_dir') . '/uploads/user/profile/' . $user->getProfileFilename();
//                    if (file_exists($oldImagePath)) {
//                        unlink($oldImagePath);
//                    }
//                }
//
//                 try {
//                    $pictureFile->move($pictureDirectory, $newFilename);
//                } catch (FileException $e) {
//                    throw new Exception($e->getMessage());
//                }
//
//                $product->setPictureFilename($newFilename);
//            }

            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }
}
