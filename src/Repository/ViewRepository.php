<?php

namespace App\Repository;

use App\Entity\View;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method View|null find($id, $lockMode = null, $lockVersion = null)
 * @method View|null findOneBy(array $criteria, array $orderBy = null)
 * @method View[]    findAll()
 * @method View[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, View::class);
    }

    /**
     * @param string $ip
     * @param integer $seconds
     * @return bool True si au moins une vue créée il y a moins de $seconds secondes a été trouvée. False sinon.
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function isFlood(string $ip, int $seconds): bool
    {
        try {
            return (bool)$this->createQueryBuilder('v')
                ->select('COUNT(v)')
                ->where('v.createdAt >= :date')
                ->setParameter('date', new \Datetime($seconds . ' seconds ago'))
                ->andWhere('v.clientIp = :ip')
                ->setParameter('ip', $ip)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return false;
        }
    }

    // /**
    //  * @return View[] Returns an array of View objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?View
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
