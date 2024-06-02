<?php

namespace App\Repository;

use App\Entity\Rental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }


    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity
     */
    public function save(Rental $rental ): void
    {
        // assert($this->_em instanceof EntityManager);
        $this->_em->persist($rental );
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Rental $rental  Rental entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rental $rental ): void
    {
        // assert($this->_em instanceof EntityManager);
        $this->_em->remove($rental );
        $this->_em->flush();
    }
    //    /**
    //     * @return Rental[] Returns an array of Rental objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rental
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
