<?php
/**
 * Book service interface.
 */

namespace App\Service;

use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;

/**
 * Interface BookServiceInterface.
 */
interface BookServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     * @param BookListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, BookListInputFiltersDto $filters): PaginationInterface;

    /**
     * Get paginated books for category.
     *
     * @param Category $category Category
     * @param int $page Page number
     *
     *  @return PaginationInterface<string, mixed> Paginated list
     */
//    public function findBooksForCategory(Category $category): array;

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

    public function setAvailable(Book $book, bool $status):void;
}// end interface
