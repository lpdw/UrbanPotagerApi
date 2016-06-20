<?php

namespace CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\Voter as BaseVoter;
use CoreBundle\Entity\Interfaces\OwnableInterface;
use UserBundle\Entity\User;

class Voter extends BaseVoter
{
    const CREATE    = 'create';
    const VIEW      = 'view';
    const EDIT      = 'edit';
    const DELETE    = 'delete';

    private $decisionManager;

    public function __construct(AccessDecisionManager $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($access, $entity, TokenInterface $token)
    {
        // ROLE_SUPER_ADMIN can do anything
        if ($this->isAdmin($token)) {
            return true;
        }

        $method = 'can' . ucfirst($access);

        if (method_exists($this, $method)) {
            return $this->$method($entity, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    protected function canCreate($entity, TokenInterface $token)
    {
        return false;
    }

    protected function canView($entity, TokenInterface $token)
    {
        return false;
    }

    protected function canEdit($entity, TokenInterface $token)
    {
        return false;
    }

    protected function canDelete($entity, TokenInterface $token)
    {
        return false;
    }

    protected function isAdmin($token)
    {
        return $this->isGranted($token, ['ROLE_SUPER_ADMIN']);
    }

    protected function isConnected($token)
    {
        return $this->isGranted($token, ['IS_AUTHENTICATED_FULLY']);
    }

    protected function isGranted(TokenInterface $token, array $roles)
    {
        return $this->decisionManager->decide($token, $roles);
    }

    protected function getUser(TokenInterface $token)
    {
        $user = $token->getUser();

        return ($user instanceof User) ? $user : null;
    }

    protected function isOwner(OwnableInterface $ownable, User $user = null)
    {
        $owner = $ownable->getOwner();

        if (is_null($owner) || is_null($user)) {
            return false;
        }

        return $owner->getId() === $user->getId();
    }
}
