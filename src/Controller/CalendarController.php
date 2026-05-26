<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\ParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('profile/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/', name: 'app_calendar')]
    public function index(): Response
    {
        return $this->render('account/calendar.html.twig');
    }

    #[Route('/calendar/events', name: 'api_calendar_events', methods: ['GET'])]
    public function events(
        ActivityRepository $activityRepository,
        ParticipationRepository $participationRepository
    ): JsonResponse {
        $user = $this->getUser();
        $events = [];

        // Activities the user CREATED
        foreach ($user->getActivities() as $activity) {
            $events[] = [
                'id'    => $activity->getId(),
                'title' => $activity->getTitle(),
                'start' => $activity->getDateStart()?->format('c'),
                'end'   => $activity->getDateEnd()?->format('c'),
                'color' => '#0d6efd',   // Bootstrap primary (blue) = created
                'extendedProps' => [
                    'type'        => 'created',
                    'location'    => $activity->getLocation(),
                    'description' => $activity->getDescription(),
                    'spots'       => $activity->getSpot(),
                    'remaining'   => $activity->getRemainingSpots(),
                    'status'      => $activity->getStatus()?->value,
                    'state'       => $activity->getState()?->value,
                ],
            ];
        }

        // Activities the user PARTICIPATES IN (avoid duplicates)
        foreach ($user->getParticipations() as $participation) {
            $activity = $participation->getActivity();
            // Skip if user also created it (already added above)
            if ($activity->getUser() === $user) {
                continue;
            }
            $events[] = [
                'id'    => 'p_' . $activity->getId(),
                'title' => $activity->getTitle(),
                'start' => $activity->getDateStart()?->format('c'),
                'end'   => $activity->getDateEnd()?->format('c'),
                'color' => '#198754',   // Bootstrap success (green) = participating
                'extendedProps' => [
                    'type'             => 'participation',
                    'participationStatus' => $participation->getStatus()?->value,
                    'location'         => $activity->getLocation(),
                    'description'      => $activity->getDescription(),
                    'organizer'        => $activity->getUser()?->getUsername(),
                ],
            ];
        }

        return $this->json($events);
    }
}
