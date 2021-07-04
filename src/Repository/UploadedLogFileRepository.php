<?php

namespace App\Repository;

use App\Entity\UploadedLogFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UploadedLogFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadedLogFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadedLogFile[]    findAll()
 * @method UploadedLogFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadedLogFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadedLogFile::class);
    }

    // /**
    //  * @return UploadedLogFile[] Returns an array of UploadedLogFile objects
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
    public function findOneBySomeField($value): ?UploadedLogFile
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
