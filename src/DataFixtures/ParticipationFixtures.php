<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Participation;
use App\Entity\User;
use App\Enum\EnumParticipationStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ParticipationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $activities = $manager->getRepository(Activity::class)->findAll();
        $users      = $manager->getRepository(User::class)->findAll();

        foreach ($activities as $activity) {
            $maxParticipants = min(rand(2, 5), $activity->getSpot());
            $participants    = array_slice(
                $users,
                array_rand($users),
                $maxParticipants
            );

            $owner = $activity->getUser();

            foreach ($participants as $user) {
                if ($user->getId() === $owner?->getId()) {
                    continue;
                }

                $participation = new Participation();
                $participation
                    ->setUser($user)
                    ->setActivity($activity)
                    ->setStatus(EnumParticipationStatus::PENDING)
                    ->setRegisteredAt(new \DateTime());

                $manager->persist($participation);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ActivityFixtures::class,
            UserFixtures::class,
        ];
    }
}
