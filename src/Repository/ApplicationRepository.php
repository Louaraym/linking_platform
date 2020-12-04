<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    /**
     * @param $limit
     * @return int|mixed|string
     */
    public function getApplicationsWithAdvert($limit)
    {
        return $this->createQueryBuilder('a')
                ->innerJoin('a.advert', 'adv')
                ->addSelect('adv')
                ->setMaxResults($limit)
                ->orderBy('a.createdAt', 'desc')
                ->getQuery()
                ->getResult()
            ;
    }

    /**
     * @param string $ip
     * @param integer $seconds
     * @return bool True si au moins une candidature créée il y a moins de $seconds secondes a été trouvée. False sinon.
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function isFlood(string $ip, int $seconds): bool
    {
        return (bool) $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.createdAt >= :date')
            ->setParameter('date', new \Datetime($seconds.' seconds ago'))
            ->andWhere('a.ip = :ip')
            ->setParameter('ip', $ip)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    // /**
    //  * @return Application[] Returns an array of Application objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Application
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
