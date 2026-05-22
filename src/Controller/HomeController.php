<?php

namespace App\Controller;

use App\Enum\EnumStatus;
use App\Repository\ActivityRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ActivityRepository $activityRepository, ThemeRepository $themeRepository): Response
    {
        $user = $this->getUser();

        $myActivities = $user
            ? $activityRepository->findUserActivities($user)
            : [];

        return $this->render('home.html.twig', [
            'my_activities' => $myActivities,
            'featured_activities' => $activityRepository->findBy(
                ['status' => EnumStatus::PUBLISHED],
                ['dateStart' => 'ASC'],
                20
            ),
            'themes' => $themeRepository->findAll(),
        ]);
    }
}
