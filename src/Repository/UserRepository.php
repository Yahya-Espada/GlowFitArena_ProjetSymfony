<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findRolesFromDataBase():array{
    $queryBuilder = $this->createQueryBuilder('u');

    // Select distinct roles from the user table
    $queryBuilder->select('DISTINCT u.role');

    $roles = $queryBuilder->getQuery()->getResult();

    // Extract roles from the result
    $roleStrings = [];
    foreach ($roles as $role) {
        $roleStrings[] = $role['role'];
    }

    return $roleStrings;
}
public function findBySearch($search):array{
    $queryBuilder = $this->createQueryBuilder('u');

    // Build the query to find users where email, phone number, or username matches the search term
    $queryBuilder->select('u')
                 ->where('u.email LIKE :search')
                 ->orWhere('u.numero_de_telephone LIKE :search')
                 ->orWhere('u.username LIKE :search')
                 ->setParameter('search', '%' . $search . '%');

    $users = $queryBuilder->getQuery()->getResult();

    return $users;
}
}
