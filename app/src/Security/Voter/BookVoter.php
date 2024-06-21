<?php
/**
 * Book voter.
 */

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * BookVoter class.
 */
class BookVoter extends Voter
{
    /**
     * View permission.
     *
     * @const string
     */
    private const VIEW = 'VIEW';
    private const RENT = 'RENT';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::RENT])
            && $subject instanceof Book;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (!$subject instanceof Book) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::RENT => $this->canRent($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can view book.
     *
     * @param Book          $book Book entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canView(Book $book, UserInterface $user): bool
    {
        // Check if the user is blocked
        return !$user->isBlocked();
    }

    /**
     * Checks if user can view book.
     *
     * @param Book          $book Book entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canRent(Book $book, UserInterface $user): bool
    {
        // Check if the user is blocked
        return !$user->isBlocked();
    }
}
