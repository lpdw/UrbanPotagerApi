<?php

namespace CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use CoreBundle\Entity\UserShare;

class UserShareVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return parent::supports($attribute, $subject) && $subject instanceof UserShare;
    }

    /**
     * @param UserShare $entity
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function canView($entity, TokenInterface $token)
    {
        return  $entity->getGarden()->getIsPublic() ||
                $this->isOwner($entity, $this->getUser($token)) ||
                $this->isOwner($entity->getGarden(), $this->getUser($token));
    }

    /**
     * @param UserShare $entity
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function canCreate($entity, TokenInterface $token)
    {
        return $this->isConnected($token);
    }

    /**
     * @param UserShare $entity
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function canEdit($entity, TokenInterface $token)
    {
        return $this->isOwner($entity, $this->getUser($token));
    }

    /**
     * @param UserShare $entity
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function canDelete($entity, TokenInterface $token)
    {
        return $this->canEdit($entity, $token);
    }
}
