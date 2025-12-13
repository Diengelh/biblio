<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ShopCreatorVoter extends Voter
{
    public const DELETE = 'shop.isCreator';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::DELETE == $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE:
                //teste si l'utilisateur est le propriétaire de l'article du panier
                return $user == $subject->getUser();
                break;

        }

        return false;
    }
}
