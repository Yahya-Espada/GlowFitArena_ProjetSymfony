<?php

namespace App\Repository;

use App\Entity\DietaryPrograms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DietaryPrograms>
 *
 * @method DietaryPrograms|null find($id, $lockMode = null, $lockVersion = null)
 * @method DietaryPrograms|null findOneBy(array $criteria, array $orderBy = null)
 * @method DietaryPrograms[]    findAll()
 * @method DietaryPrograms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DietaryProgramsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DietaryPrograms::class);
    }

//    /**
//     * @return DietaryPrograms[] Returns an array of DietaryPrograms objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DietaryPrograms
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Find dietary programs by subscriber ID.
     *
     * @param int $subscriberId
     * @return DietaryPrograms[]
     */
    public function findBySubscriberId(int $subscriberId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.subscriber = :subscriberId')
            ->setParameter('subscriberId', $subscriberId)
            ->getQuery()
            ->getResult();
    }
}
