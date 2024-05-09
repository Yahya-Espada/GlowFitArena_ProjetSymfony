<?php

namespace App\Repository;

use App\Entity\SubscriberInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubscriberInfo>
 *
 * @method SubscriberInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriberInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriberInfo[]    findAll()
 * @method SubscriberInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriberInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriberInfo::class);
    }

//    /**
//     * @return SubscriberInfo[] Returns an array of SubscriberInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SubscriberInfo
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function get_user_info($subscriber_id): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :val')
            ->setParameter('val', $subscriber_id)
            ->getQuery()
            ->getResult();

    }

    /**
     * Find subscriber info entities by user_id.
     *
     * @param int $userId
     * @return SubscriberInfo[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

}
