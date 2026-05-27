<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\ParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Asset\Packages;

#[IsGranted('ROLE_USER')]
#[Route('/profile/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/', name: 'app_calendar')]
    public function index(): Response
    {
        return $this->render('account/calendar.html.twig');
    }

    #[Route('/calendar/events', name: 'api_calendar_events', methods: ['GET'])]
    public function events(
        ActivityRepository      $activityRepository,
        ParticipationRepository $participationRepository,
        Packages                $assets
    ): JsonResponse
    {
        $user = $this->getUser();
        $events = [];

        foreach ($user->getActivities() as $activity) {
            $themes = $activity->getThemes();
            $firstTheme = !$themes->isEmpty() ? $themes->first() : null;


            $events[] = [
                'id' => $activity->getId(),
                'title' => $activity->getTitle(),
                'start' => $activity->getDateStart()?->format('c'),
                'end' => $activity->getDateEnd()?->format('c'),
                'backgroundColor' => 'var(--primary-color)',
                'borderColor' => 'var(--primary-color)',
                'textColor' => 'var(--texte-fonce)',
                'extendedProps' => [
                    'type' => 'created',
                    'location' => $activity->getLocation(),
                    'description' => $activity->getDescription(),
                    'spots' => $activity->getSpot(),
                    'remaining' => $activity->getRemainingSpots(),
                    'status' => $activity->getStatus()?->value,
                    'state' => $activity->getState()?->value,
                    'icon' => $firstTheme !== null
                        ? $assets->getUrl($firstTheme->getIconFilename())
                        : null,
                ],
            ];
        }

        foreach ($user->getParticipations() as $participation) {
            $activity = $participation->getActivity();
            if ($activity->getUser() === $user) {
                continue;
            }

            $themes = $activity->getThemes();
            $firstTheme = !$themes->isEmpty() ? $themes->first() : null;


            $events[] = [
                'id' => 'p_' . $activity->getId(),
                'title' => $activity->getTitle(),
                'start' => $activity->getDateStart()?->format('c'),
                'end' => $activity->getDateEnd()?->format('c'),
                'backgroundColor' => 'var(--quaternary-color)',
                'borderColor' => 'var(--quaternary-color)',
                'textColor' => 'var(--texte-clair)',
                'extendedProps' => [
                    'type' => 'participation',
                    'participationStatus' => $participation->getStatus()?->value,
                    'location' => $activity->getLocation(),
                    'description' => $activity->getDescription(),
                    'organizer' => $activity->getUser()?->getUsername(),
                    'icon' => $firstTheme !== null
                        ? $assets->getUrl($firstTheme->getIconFilename())
                        : null,
                ],
            ];
        }

        return $this->json($events);
    }
}
