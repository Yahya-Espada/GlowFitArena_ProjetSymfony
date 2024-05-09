<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findBySearchAndSort(string $searchQuery, string $sortBy, string $sortOrder): array
{
    $queryBuilder = $this->createQueryBuilder('p')
        ->andWhere('p.libelle LIKE :searchQuery')
        ->setParameter('searchQuery', '%' . $searchQuery . '%');

    // Add sorting
    $queryBuilder->orderBy('p.idProduit', $sortOrder);

    return $queryBuilder->getQuery()->getResult();
}

public function findAllSorted(string $sortBy = 'idProduit', string $sortOrder = 'ASC'): array
{
    $validFields = ['idProduit', 'libelle', 'quantite', 'prix'];

    // Check if the provided field for sorting is valid
    if (!in_array($sortBy, $validFields)) {
        throw new \InvalidArgumentException('Invalid field for sorting');
    }

    $queryBuilder = $this->createQueryBuilder('p');

    // Add sorting based on the provided field
    $queryBuilder->orderBy('p.' . $sortBy, $sortOrder);

    return $queryBuilder->getQuery()->getResult();
}

/**
     * @return Produit[] Returns an array of Produit objects
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.idProduit', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function searchByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.libelle LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }

    public function getProductStats(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.libelle AS libelle, SUM(p.quantite) AS quantite')
            ->groupBy('p.libelle')
            ->getQuery()
            ->getResult();
    }


}

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

