<?php

namespace App\Repository;

use App\Entity\Rental;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
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
     * Query rental by status
     *
     * @return QueryBuilder Query builder
     */
    public function queryByStatus(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select (
                'partial rental.{id, owner, book, status, rentalDate}',
                'partial user.{id, email}',
                'partial book.{id, title}'
            )
            ->join('rental.book', 'book')
            ->join('rental.owner', 'user')
            ->where ('rental.status = :status')
            ->setParameter('status', false);

    }
    /**
     * Query rental by owner
     *
     * @return QueryBuilder Query builder
     */
    public function queryByOwner(User $user): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select (
                'partial rental.{id, owner, book, status, rentalDate}',
                'partial user.{id, email}',
                'partial book.{id, title, owner}'
            )
            ->join('rental.book', 'book')
            ->join('rental.owner', 'user')
            ->where('rental.owner = :user')
            ->andWhere('book.owner = :user')
            ->setParameter('user', $user);


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
    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('rental');
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
