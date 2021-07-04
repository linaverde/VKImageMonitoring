<?php

namespace App\Repository;

use App\Entity\UserMonitoringSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMonitoringSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMonitoringSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMonitoringSettings[]    findAll()
 * @method UserMonitoringSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMonitoringSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMonitoringSettings::class);
    }

    // /**
    //  * @return UserMonitoringSettings[] Returns an array of UserMonitoringSettings objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserMonitoringSettings
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
