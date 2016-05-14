<?php

namespace CoreBundle\Model;

class CollectionAccess
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $access;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->access = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param mixed $access
     *
     * @return CollectionAccess
     */
    public function setAccess($access)
    {
        $this->access = $access;

        return $this;
    }
}
