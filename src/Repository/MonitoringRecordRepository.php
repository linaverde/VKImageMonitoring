<?php

namespace App\Repository;

use App\Entity\MonitoringRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MonitoringRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method MonitoringRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method MonitoringRecord[]    findAll()
 * @method MonitoringRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonitoringRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonitoringRecord::class);
    }

    // /**
    //  * @return MonitoringRecord[] Returns an array of MonitoringRecord objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MonitoringRecord
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
