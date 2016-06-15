<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * UserShare
 *
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\UserShareRepository")
 */
class UserShare extends Share
{
    /**
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @JMS\Expose()
     */
    private $owner;

    /**
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     *
     * @return UserShare
     */
    public function setOwner(\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
