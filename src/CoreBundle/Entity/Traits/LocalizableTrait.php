<?php

namespace CoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Class LocalizableTrait
 * @package CoreBundle\Entity\Traits
 *
 * @JMS\ExclusionPolicy("all")
 */
Trait LocalizableTrait
{
    /**
     * @var float $latitude
     *
     * @ORM\Column(name="latitude", type="decimal", precision=13, scale=10)
     * @Assert\NotNull()
     * @Assert\Type(
     *     type="float"
     * )
     * @JMS\Expose()
     */
    private $latitude;

    /**
     * @var float $longitude
     *
     * @ORM\Column(name="longitude", type="decimal", precision=13, scale=10)
     * @Assert\NotNull()
     * @Assert\Type(
     *     type="float"
     * )
     * @JMS\Expose()
     */
    private $longitude;

    /**
     * @var boolean $showLocation
     *
     * @ORM\Column(name="showLocation", type="boolean")
     * @Assert\Type(
     *     type="bool"
     * )
     * @JMS\Expose()
     * @JMS\Groups({"me-garden"})
     */
    private $showLocation;

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     *
     * @return $this
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     *
     * @return $this
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowLocation()
    {
        return $this->showLocation;
    }

    /**
     * @param boolean $showLocation
     *
     * @return $this
     */
    public function setShowLocation($showLocation)
    {
        $this->showLocation = $showLocation;

        return $this;
    }
}
