<?php

namespace CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use CoreBundle\Entity\Garden;
use UserBundle\Entity\User;

class GardenVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return parent::supports($attribute, $subject) && $subject instanceof Garden;
    }

    /**
     * @param Garden $entity
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function canView($entity, TokenInterface $token)
    {
        return $entity->getIsPublic() || $this->isOwner($entity, $this->getUser($token));
    }

    /**
     * @param Garden $entity
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function canCreate($entity, TokenInterface $token)
    {
        return $this->isConnected($token);
    }

    /**
     * @param Garden $entity
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function canEdit($entity, TokenInterface $token)
    {
        return $this->isOwner($entity, $this->getUser($token));
    }

    /**
     * @param Garden $entity
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function canDelete($entity, TokenInterface $token)
    {
        return $this->canEdit($entity, $token);
    }
}
