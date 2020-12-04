<?php

namespace App\Repository;

use App\Entity\Advert;
use Datetime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    /**
     * @return Advert[] Returns an array of Advert objects
     * @param $limit
     */
    public function findLastAdverts($limit): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.published = 1')
            ->orderBy('a.id', 'desc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Advert[] Returns an array of Advert objects
     */
    public function findAllAdverts(): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.author', 'auth')
            ->addSelect('auth')
            ->where('a.published = 1')
            ->orderBy('a.id', 'desc')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $id
     * @return object|null The entity instance or NULL if the entity can not be found.
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneAdvert($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->innerJoin('a.author', 'auth')
            ->addSelect('auth')
            ->innerJoin('a.views', 'v')
            ->addSelect('v')
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ->leftJoin('a.image', 'i')
            ->addSelect('i')
            ->getQuery()
            ->getSingleResult()
            ;
    }

    /**
     * @param $author
     * @param $year
     * @return int|mixed|string
     */
    public function findByAuthorAndDate($author, $year)
    {
        return $this->createQueryBuilder('a')
            ->where('a.author = :author')
            ->setParameter('author', $author)
            ->andWhere('a.createdAt < :year')
            ->setParameter('year', $year)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param QueryBuilder $qb
     * @throws Exception
     */
    public function whereCurrentYear(QueryBuilder $qb): void
    {
        $qb
            ->andWhere('a.createdAt BETWEEN :start AND :end')
            ->setParameter('start', new Datetime(date('Y').'-01-01'))  // Date entre le 1er janvier de cette année
            ->setParameter('end',   new Datetime(date('Y').'-12-31'))  // Et le 31 décembre de cette année
        ;
    }

    /**
     * @param $author
     * @return int|mixed|string
     * @throws Exception
     */
    public function findByCurrentYear($author)
    {
        $qb = $this->createQueryBuilder('a');

        // On peut ajouter ce qu'on veut avant
        $qb
            ->where('a.author = :author')
            ->setParameter('author', $author)
            ->select('a.title')
        ;

        // On applique notre condition sur le QueryBuilder
        $this->whereCurrentYear($qb);

        // On peut ajouter ce qu'on veut après
        $qb->orderBy('a.id', 'DESC');

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param array $categoryNames
     * @return int|mixed|string
     */
    public function getAdvertWithCategories(array $categoryNames)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->innerJoin('a.categories', 'c')
            ->addSelect('c')
            ->andWhere($qb->expr()->in('c.name', $categoryNames))
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param Datetime $date
     * @return Advert[] Returns an array of Advert objects
     */
    public function findAdvertsBefore(Datetime $date): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.updatedAt <= :date') // Date de modification antérieure à :date
            ->orWhere('a.updatedAt IS NULL AND a.createdAt <= :date') // Si la date de modification est vide, on vérifie la date de création
            ->andWhere('a.applications IS EMPTY')                // On vérifie que l'annonce ne contient aucune candidature
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param Datetime $date
     * @return Advert[] Returns an array of Advert objects
     */
    public function findAdvertsAfter(Datetime $date): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.updatedAt >= :date') // Date de modification ulterieure à :date
            ->orWhere('a.updatedAt IS NULL AND a.createdAt >= :date') // Si la date de modification est vide, on vérifie la date de création
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Advert[] Returns an array of Advert objects
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
    public function findOneBySomeField($value): ?Advert
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
