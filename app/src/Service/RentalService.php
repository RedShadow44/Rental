<?php
/**
 * Task service.
 */

namespace App\Service;

use App\Dto\BookListFiltersDto;
use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Rental;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\RentalRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TaskService.
 */
class RentalService implements RentalServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param RentalRepository $rentalRepository Task repository
     * @param PaginatorInterface $paginator Paginator
     */
    public function __construct(private readonly RentalRepository $rentalRepository, private readonly BookRepository $bookRepository, private readonly PaginatorInterface $paginator)
    {
    }// end __construct()



//    public function rentBook(Rental $rental): void
//    {
//        $book=$rental->getBook();
//        $book->setAvailable(false);
//
//        $this->rentalRepository->save($rental);
//        $this->bookRepository->save($book);
//
//
//    }

    /**
     * Rent a book
     *
     * @param Book $book Book entity
     * @param $user
     *
     * @return Rental Rental entity
     */
    public function rentBook(Book $book, $user):Rental
    {
        $rental = new Rental();
        $rental->setOwner($user);
        $rental->setBook($book);
        $rental->setStatus(false);

        $book->setAvailable(false);

        $this->rentalRepository->save($rental);
        $this->bookRepository->save($book);

        return $rental;
    }

    /**
     *
     * Approve book rental
     *
     * @param Rental $rental
     * @return void
     */
    public function approveRental(Rental $rental): void
    {
        $user = $rental->getOwner();

        $rental->setStatus(true);

        $book = $rental->getBook();
        $book->setOwner($user);

        $this->rentalRepository->save($rental);
        $this->bookRepository->save($book);
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedByStatus(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->rentalRepository->queryByStatus(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }// end getPaginatedList()

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedByOwner(int $page, int $owner): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->rentalRepository->queryByOwner($owner),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }// end getPaginatedList()

    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rental $rental): void
    {

        $this->rentalRepository->save($rental);
    }// end save()

    /**
     * Delete entity.
     *
     * @param Rental $rental Rental entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rental $rental): void
    {
        $this->rentalRepository->delete($rental);
    }// end delete()

}