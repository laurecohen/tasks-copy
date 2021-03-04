<?php

namespace App\Repository;

use App\Entity\RegistrationTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RegistrationTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistrationTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistrationTask[]    findAll()
 * @method RegistrationTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistrationTask::class);
    }

    // /**
    //  * @return RegistrationTask[] Returns an array of RegistrationTask objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RegistrationTask
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
