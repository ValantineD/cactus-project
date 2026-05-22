<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function messagerie(User $user): array
    {
        return $this->createQueryBuilder('message')
            ->where('message.sender = :user OR message.receiver = :user')
            ->setParameter('user', $user)
            ->orderBy('message.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findConversation(User $userA, User $userB): array
    {
        $expr = $this->createQueryBuilder('message')->expr();

        return $this->createQueryBuilder('message')
            ->where(
                $expr->orX(
                    $expr->andX(
                        $expr->eq('message.sender', ':userA'),
                        $expr->eq('message.receiver', ':userB')
                    ),
                    $expr->andX(
                        $expr->eq('message.sender', ':userB'),
                        $expr->eq('message.receiver', ':userA')
                    )
                )
            )
            ->setParameter('userA', $userA)
            ->setParameter('userB', $userB)
            ->orderBy('message.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

