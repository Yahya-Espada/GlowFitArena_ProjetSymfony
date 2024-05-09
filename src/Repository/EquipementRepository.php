<?php

namespace App\Repository;

use App\Entity\Equipement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipement>
 *
 * @method Equipement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipement[]    findAll()
 * @method Equipement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipement::class);
    }
    public function trie()
    {
        return $this->createQueryBuilder('equipement')
            ->orderBy('equipement.rate', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function countByType()
    {
        return $this->createQueryBuilder('r')
            ->select('r.rate, COUNT(r.id) as count')
            ->groupBy('r.rate')
            ->getQuery()
            ->getResult();
    }

    public function trieDes()
    {
        return $this->createQueryBuilder('equipement')
            ->orderBy('equipement.rate', 'DESC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Equipement[] Returns an array of Equipement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Equipement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function getReservationStatusCounts(): array
{
    return $this->createQueryBuilder('r')
    ->select('r.rate', 'COUNT(r.id) as rateCount')
    ->groupBy('r.rate')
    ->getQuery()
    ->getResult();
}





}
