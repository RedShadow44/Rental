<?php
/**
 * Rental service interface.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Rental;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface RentalServiceInterface.
 */
interface RentalServiceInterface
{
    /**
     * Rent a book.
     *
     * @param Book $book Book entity
     * @param User $user User entity
     *
     * @return Rental Rental entity
     */
    public function rentBook(Book $book, User $user): Rental;

    /**
     * Approve book rental.
     *
     * @param Rental $rental Rental entity
     */
    public function approveRental(Rental $rental): void;

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedByStatus(int $page): PaginationInterface;

    /**
     * Get paginated list.
     *
     * @param int $page  Page number
     * @param int $owner Owner id
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedByOwner(int $page, int $owner): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity
     */
    public function save(Rental $rental): void;

    /**
     * Delete entity.
     *
     * @param Rental $rental Book entity
     */
    public function delete(Rental $rental): void;
}
