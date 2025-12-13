<?php

namespace App\Security\Voter;

use App\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BookCreatorVoter extends Voter
{
    public const EDIT_BOOK = 'book.is_creator';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return self::EDIT_BOOK == $attribute
              && $subject instanceof Book;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) { // On pouvait mettre User à la place de UserInterface
            return false;                      // car ils sont compatibles
        }

        //si l'utilisateur est un administrateur ou est le créateur du livre
        // en question on fait voter le Voter oui sinon on le fait voter false.
        return in_array("ROLE_ADMIN", $user->getRoles()) || $subject->getCreatedBy() == $user;
    }
}
