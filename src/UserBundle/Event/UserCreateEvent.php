<?php

namespace UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use UserBundle\Entity\User;

/**
 * Class UserCreateEvent
 *
 * @package UserBundle\Event
 */
class UserCreateEvent extends Event
{
    const NAME = 'user.created';

    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
