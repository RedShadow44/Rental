<?php
/**
 * Book service interface.
 */

namespace App\Service;

use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface BookServiceInterface.
 */
interface BookServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int                     $page    Page number
     * @param BookListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, BookListInputFiltersDto $filters): PaginationInterface;

    /**
     * Get paginated books for category.
     *
     * @param int      $page     Page number
     * @param Category $category Category
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedBooksForCategory(int $page, Category $category): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Book $book book entity
     */
    public function save(Book $book): void;

    /**
     * Delete entity.
     *
     * @param Book $book Book entity
     */
    public function delete(Book $book): void;

    /**
     * Set available action.
     *
     * @param Book $book   Book entity
     * @param bool $status Status
     */
    public function setAvailable(Book $book, bool $status): void;
}// end interface
