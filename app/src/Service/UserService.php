<?php
/**
 * User service.
 */

namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\BookRepository;
use App\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class NoteService.
 */
class UserService implements UserServiceInterface
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
     * @param UserRepository     $userRepository     BUser repository
     * @param PaginatorInterface $paginator          Paginator
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);

    }


    /**
     * Delete entity.
     *
     * @param User $user User entity
     *
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(User $user): void
    {
        $this->userRepository->delete($user);

    }

    public function isLastAdmin(User $user): bool
    {
        $admins = $this->userRepository->findByRole('ROLE_ADMIN');
        return count($admins) === 1 && $admins[0] === $user;
    }

}