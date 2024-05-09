<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }






    public function findrepasByStats($stats)
{
    $queryBuilder = $this->createQueryBuilder('c');

    if (!empty($stats)) {
        // Perform a case-insensitive search by converting both the search query and the database field to lowercase
        $queryBuilder->where('LOWER(c.stats) LIKE :stats')
                     ->setParameter('stats', '%' . strtolower($stats) . '%');
    }

    return $queryBuilder->getQuery()->getResult();
}
public function getRepasStatistics(): array
{
    $disponibleCount = $this->createQueryBuilder('p')
        ->select('count(p.id)')
        ->where('p.etat = :etat')
        ->setParameter('etat', '0')
        ->getQuery()
        ->getSingleScalarResult();

    $nondisponibleCount = $this->createQueryBuilder('p')
        ->select('count(p.id)')
        ->where('p.etat = :etat')
        ->setParameter('etat', '1')
        ->getQuery()
        ->getSingleScalarResult();

    return [
        '0' => $disponibleCount,
        '1' => $nondisponibleCount,
    ];
}












}
