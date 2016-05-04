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
Trait AddressableTrait
{
    /**
     * @var string
     *
     * @ORM\Column(length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="constraints.length.max")
     * @JMS\Expose()
     * @JMS\Groups({"detail-garden"})
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="constraints.length.max")
     * @JMS\Expose()
     * @JMS\Groups({"detail-garden"})
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(length=10, nullable=true)
     * @Assert\Length(max=10, maxMessage="constraints.length.max")
     * @JMS\Expose()
     * @JMS\Groups({"detail-garden"})
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="constraints.length.max")
     * @JMS\Expose()
     * @JMS\Groups({"detail-garden"})
     */
    private $address1;

    /**
     * @var string
     *
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="constraints.length.max")
     * @JMS\Expose()
     * @JMS\Groups({"detail-garden"})
     */
    private $address2;
}
