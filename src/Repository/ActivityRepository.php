<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Enum\EnumStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function findBySearch(?string $localisation, ?string $activite, ?string $date, array $themes = [], array $tags = []): array
    {
        $query = $this->createQueryBuilder('searchedActivity')
            ->where('searchedActivity.status = :status')
            ->setParameter('status', EnumStatus::PUBLISHED);

        if (!empty($localisation)) {
            $query->andWhere('searchedActivity.location LIKE :location')
                ->setParameter('location', '%' . $localisation . '%');
        }

        if (!empty($activite)) {
            $query->andWhere(
                $query->expr()->orX(
                    'searchedActivity.title LIKE :searchedActivity',
                    'searchedActivity.description LIKE :searchedActivity'
                )
            )
                ->setParameter('searchedActivity', '%' . $activite . '%');
        }

        if (!empty($date)) {
            $newDate = \DateTime::createFromFormat('Y-m-d', $date);
            if ($newDate) {
                $query->andWhere('searchedActivity.dateStart >= :dateStart')
                    ->setParameter('dateStart', $newDate->setTime(0, 0, 0));
            }
        }

        if (!empty($themes)) {
            $query->join('searchedActivity.themes', 't')
                ->andWhere('t.id IN (:themes)')
                ->setParameter('themes', $themes);
        }

        if (!empty($tags)) {
            foreach ($tags as $index => $tag) {
                $query->andWhere('searchedActivity.tags LIKE :tag' . $index)
                    ->setParameter('tag' . $index, '%' . $tag . '%');
            }
        }

        return $query->orderBy('searchedActivity.dateStart', 'ASC')->getQuery()->getResult();
    }
}

