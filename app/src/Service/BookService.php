<?php
/**
 * Book service.
 */

namespace App\Service;

use App\Dto\BookListFiltersDto;
use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Repository\BookRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class BookService.
 */
class BookService implements BookServiceInterface
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
     * @param BookRepository           $bookRepository  Task repository
     * @param PaginatorInterface       $paginator       Paginator
     * @param TagServiceInterface      $tagService      Tag service
     * @param CategoryServiceInterface $categoryService Category service
     */
    public function __construct(private readonly BookRepository $bookRepository, private readonly PaginatorInterface $paginator, private readonly TagServiceInterface $tagService, private readonly CategoryServiceInterface $categoryService)
    {
    }// end __construct()

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     * @param BookListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, BookListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->bookRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }// end getPaginatedList()

    /**
     * Get paginated books for category.
     *
     * @param Category $category Category
     * @param int $page Page number
     *
     *  @return PaginationInterface<string, mixed> Paginated list
    */
//    public function findBooksForCategory(Category $category): array
//    {
//        return $this->bookRepository->findBooksForCategory($category);
//    }// end findBooksForCategory()
    public function getPaginatedBooksForCategory(int $page, Category $category): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->bookRepository->findBooksForCategory($category),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Book $book Book entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Book $book): void
    {
        // if (null === $book->getId()) {
        // $book->setCreatedAt(new \DateTimeImmutable());
        // }
        // $book->setUpdatedAt(new \DateTimeImmutable());
        $this->bookRepository->save($book);
    }// end save()

    /**
     * Delete entity.
     *
     * @param Book $book Book entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Book $book): void
    {
        $this->bookRepository->delete($book);
    }// end delete()

    /**
     * Prepare filters for the tasks list.
     *
     * @param BookListInputFiltersDto $filters Raw filters from request
     *
     * @return BookListFiltersDto Result filters
     */
    private function prepareFilters(BookListInputFiltersDto $filters): BookListFiltersDto
    {
        return new BookListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
        );
    }
}// end class
