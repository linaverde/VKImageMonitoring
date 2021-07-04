<?php

namespace App\Repository;

use App\Entity\SubsctiberInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubsctiberInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubsctiberInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubsctiberInfo[]    findAll()
 * @method SubsctiberInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubsctiberInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubsctiberInfo::class);
    }

    // /**
    //  * @return SubsctiberInfo[] Returns an array of SubsctiberInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubsctiberInfo
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
