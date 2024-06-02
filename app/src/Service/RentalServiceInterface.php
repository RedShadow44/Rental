<?php
/**
 * Rental service interface.
 */

namespace App\Service;

use App\Entity\Rental;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
/**
* Interface RentalServiceInterface.
*/
interface RentalServiceInterface
{
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
    public function getPaginatedByOwner(int $page): PaginationInterface;

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