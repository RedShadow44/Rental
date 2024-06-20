<?php
/**
 * Rental service interface.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Rental;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
/**
* Interface RentalServiceInterface.
*/
interface RentalServiceInterface
{
    /**
     * Rent a book
     *
     * @param Book $book Book entity
     * @param $user
     *
     * @return Rental Rental entity
     */
    public function rentBook(Book $book, $user):Rental;

    /**
     *
     * Approve book rental
     *
     * @param Rental $rental
     * @return void
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
     * @param int $page Page number
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