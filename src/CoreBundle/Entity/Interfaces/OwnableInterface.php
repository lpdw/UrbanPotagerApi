<?php

namespace CoreBundle\Entity\Interfaces;

interface OwnableInterface
{
    /**
     * @return \UserBundle\Entity\User
     */
    public function getOwner();
}
