<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileVoter extends Voter
{
    /**
     * View permission.
     *
     * @const string
     */
    private const VIEW = 'view_profile';

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
        return in_array($attribute, [self::VIEW])
            && $subject instanceof User;
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
        $authenticatedUser = $token->getUser();

        if (!$authenticatedUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // Check if the user is viewing their own profile
        /** @var User $profileUser */
        $profileUser = $subject;

        return $authenticatedUser->getId() === $profileUser->getId();
    }


}
