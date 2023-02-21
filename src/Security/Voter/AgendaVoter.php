<?php

namespace App\Security\Voter;

use App\Entity\Agenda;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AgendaVoter extends Voter
{
    public const EDIT = 'AGENDA_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof Agenda;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        \assert($subject instanceof Agenda);

        $user = $token->getUser();

        return $user instanceof User
            ? $user->equals($subject->getOwner())
            : false;
    }
}
