<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Chat>
 *
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function findConversation(User $user, User $otherUser)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('(m.sender = :user AND m.receiver = :otherUser) OR (m.sender = :otherUser AND m.receiver = :user)')
            ->setParameter('user', $user)
            ->setParameter('otherUser', $otherUser)
            ->orderBy('m.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByReceiver(User $user)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :user')
            ->setParameter('user', $user)
            ->orderBy('m.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySender(User $user)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.sender = :user')
            ->setParameter('user', $user)
            ->orderBy('m.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findMessageNonLu(User $user)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :user')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Chat[] Returns an array of Chat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Chat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
