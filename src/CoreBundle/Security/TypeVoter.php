<?php

namespace CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use CoreBundle\Entity\Type;

class TypeVoter extends Voter
{
    const CREATE = 'create';

    protected function supports($attribute, $subject)
    {
        return (parent::supports($attribute, $subject) || self::CREATE === $attribute) && $subject instanceof Type;
    }

    protected function canView($entity, TokenInterface $token)
    {
        return true;
    }

    // canEdit & canDelete is not override because Voter::voteOnAttribute return true for super_admin (and only super_admin can edit/delete type)
    // canCreate is not implement for the same reason
}
