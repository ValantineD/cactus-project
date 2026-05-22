<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\User;
use App\Enum\EnumParticipationStatus;
use App\Enum\EnumStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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

    public function findBySearch(
        ?string $activite,
        ?string $date,
        array   $themes = [],
        array   $tags = [],
        float   $radius = 30,
        ?float  $lat = null,
        ?float  $lng = null
    ): array {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata($this->getEntityName(), 'a');

        if ($lat !== null && $lng !== null) {
            $sql = 'SELECT ' . $rsm->generateSelectClause(['a' => 'a']) . ',
                ST_Distance_Sphere(
                    POINT(a.longitude, a.latitude),
                    POINT(:lng, :lat)
                ) / 1000 AS distance';
        } else {
            $sql = 'SELECT ' . $rsm->generateSelectClause(['a' => 'a']);
        }

        $sql .= ' FROM activity a WHERE a.status = :status';
        $params = ['status' => EnumStatus::PUBLISHED->value];

        if ($lat !== null && $lng !== null) {
            $sql .= ' AND a.latitude IS NOT NULL AND a.longitude IS NOT NULL';
            $params['lat'] = $lat;
            $params['lng'] = $lng;
            $params['radius'] = $radius;
        }

        if (!empty($activite)) {
            $sql .= ' AND (a.title LIKE :activite OR a.description LIKE :activite)';
            $params['activite'] = '%' . $activite . '%';
        }

        if (!empty($date)) {
            $newDate = \DateTime::createFromFormat('Y-m-d', $date);
            if ($newDate) {
                $sql .= ' AND a.date_start >= :dateStart';
                $params['dateStart'] = $newDate->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            }
        }

        if (!empty($themes)) {
            $sql .= ' AND a.id IN (SELECT at.activity_id FROM activity_theme at WHERE at.theme_id IN (:themes))';
            $params['themes'] = implode(',', $themes);
        }

        if (!empty($tags)) {
            foreach ($tags as $index => $tag) {
                $sql .= ' AND a.tags LIKE :tag' . $index;
                $params['tag' . $index] = '%' . $tag . '%';
            }
        }

        if ($lat !== null && $lng !== null) {
            $sql .= ' HAVING distance < :radius ORDER BY distance ASC';
        } else {
            $sql .= ' ORDER BY a.date_start ASC';
        }

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        foreach ($params as $key => $value) {
            $query->setParameter($key, $value);
        }

        return $query->getResult();
    }

    public function findUserActivities(User $user): array
    {
        return $this->createQueryBuilder('activity')
            ->distinct()
            ->leftJoin('activity.participations', 'participation')
            ->where('activity.user = :user')
            ->orWhere('participation.user = :user AND participation.status != :cancelled')
            ->andWhere('activity.status = :status')
            ->setParameter('user', $user)
            ->setParameter('cancelled', EnumParticipationStatus::CANCELLED)
            ->setParameter('status', EnumStatus::PUBLISHED)
            ->orderBy('activity.dateStart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCreatedByUser(User $user): array
    {
        return $this->createQueryBuilder('activity')
            ->where('activity.user = :user')
            ->andWhere('activity.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', EnumStatus::PUBLISHED)
            ->orderBy('activity.dateStart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findParticipatedByUser(User $user): array
    {
        return $this->createQueryBuilder('activity')
            ->join('activity.participations', 'participation')
            ->where('participation.user = :user')
            ->andWhere('participation.status != :cancelled')
            ->andWhere('activity.status = :status')
            ->setParameter('user', $user)
            ->setParameter('cancelled', \App\Enum\EnumParticipationStatus::CANCELLED)
            ->setParameter('status', EnumStatus::PUBLISHED)
            ->orderBy('activity.dateStart', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

