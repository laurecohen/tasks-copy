<?php

namespace App\Repository;

use App\Entity\TaskTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTemplate[]    findAll()
 * @method TaskTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTemplate::class);
    }

    // /**
    //  * @return TaskTemplate[] Returns an array of TaskTemplate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskTemplate
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
