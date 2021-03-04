<?php

namespace App\Repository;

use App\Entity\TaskTemplateSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskTemplateSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTemplateSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTemplateSkill[]    findAll()
 * @method TaskTemplateSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTemplateSkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTemplateSkill::class);
    }

    // /**
    //  * @return TaskTemplateSkill[] Returns an array of TaskTemplateSkill objects
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
    public function findOneBySomeField($value): ?TaskTemplateSkill
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
