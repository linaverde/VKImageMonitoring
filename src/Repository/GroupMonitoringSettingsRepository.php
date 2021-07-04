<?php

namespace App\Repository;

use App\Entity\GroupMonitoringSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupMonitoringSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupMonitoringSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupMonitoringSettings[]    findAll()
 * @method GroupMonitoringSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupMonitoringSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMonitoringSettings::class);
    }

    // /**
    //  * @return GroupMonitoringSettings[] Returns an array of GroupMonitoringSettings objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupMonitoringSettings
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
