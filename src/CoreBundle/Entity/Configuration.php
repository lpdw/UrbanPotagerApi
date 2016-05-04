<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * Configuration
 *
 * @ORM\Table(name="configuration")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ConfigurationRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Configuration
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank(message="constraints.not_blank")
     * @Assert\Length(max=250, maxMessage="constraints.length.max")
     * @JMS\Expose()
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     * @JMS\Expose()
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotNull(message="constraints.not_null")
     * @JMS\Expose()
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\Type(
     *     type="float",
     *     message="constraints.type"
     * )
     * @Assert\Range(
     *      min = 0.00,
     *      max = 100.00,
     *      minMessage = "constraints.range.min",
     *      maxMessage = "constraints.range.max",
     * )
     * @JMS\Expose()
     * @JMS\Groups({"detail-configuration"})
     */
    private $lightTreshold;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time")
     * @Assert\Time(message="constraint.time")
     * @JMS\Expose()
     * @JMS\Groups({"detail-configuration"})
     */
    private $lightingStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time")
     * @Assert\Time(message="constraint.time")
     * @JMS\Expose()
     * @JMS\Groups({"detail-configuration"})
     */
    private $lightingEnd;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Assert\Type(
     *     type="bool",
     *     message="constraints.type"
     * )
     * @JMS\Expose()
     * @JMS\Groups({"detail-configuration"})
     */
    private $isWateringActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time")
     * @Assert\Time(message="constraint.time")
     * @JMS\Expose()
     * @JMS\Groups({"detail-configuration"})
     */
    private $wateringStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time")
     * @Assert\Time(message="constraint.time")
     * @JMS\Expose()
     * @JMS\Groups({"detail-configuration"})
     */
    private $wateringEnd;

    /**
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     * @JMS\Expose()
     */
    private $owner;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Configuration
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Configuration
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set lightTreshold
     *
     * @param string $lightTreshold
     *
     * @return Configuration
     */
    public function setLightTreshold($lightTreshold)
    {
        $this->lightTreshold = $lightTreshold;

        return $this;
    }

    /**
     * Get lightTreshold
     *
     * @return string
     */
    public function getLightTreshold()
    {
        return $this->lightTreshold;
    }

    /**
     * Set lightingStart
     *
     * @param \DateTime $lightingStart
     *
     * @return Configuration
     */
    public function setLightingStart($lightingStart)
    {
        $this->lightingStart = $lightingStart;

        return $this;
    }

    /**
     * Get lightingStart
     *
     * @return \DateTime
     */
    public function getLightingStart()
    {
        return $this->lightingStart;
    }

    /**
     * Set lightingEnd
     *
     * @param \DateTime $lightingEnd
     *
     * @return Configuration
     */
    public function setLightingEnd($lightingEnd)
    {
        $this->lightingEnd = $lightingEnd;

        return $this;
    }

    /**
     * Get lightingEnd
     *
     * @return \DateTime
     */
    public function getLightingEnd()
    {
        return $this->lightingEnd;
    }

    /**
     * Set isWateringActive
     *
     * @param boolean $isWateringActive
     *
     * @return Configuration
     */
    public function setIsWateringActive($isWateringActive)
    {
        $this->isWateringActive = $isWateringActive;

        return $this;
    }

    /**
     * Get isWateringActive
     *
     * @return bool
     */
    public function getIsWateringActive()
    {
        return $this->isWateringActive;
    }

    /**
     * Set wateringStart
     *
     * @param \DateTime $wateringStart
     *
     * @return Configuration
     */
    public function setWateringStart($wateringStart)
    {
        $this->wateringStart = $wateringStart;

        return $this;
    }

    /**
     * Get wateringStart
     *
     * @return \DateTime
     */
    public function getWateringStart()
    {
        return $this->wateringStart;
    }

    /**
     * Set wateringEnd
     *
     * @param \DateTime $wateringEnd
     *
     * @return Configuration
     */
    public function setWateringEnd($wateringEnd)
    {
        $this->wateringEnd = $wateringEnd;

        return $this;
    }

    /**
     * Get wateringEnd
     *
     * @return \DateTime
     */
    public function getWateringEnd()
    {
        return $this->wateringEnd;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return Configuration
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     *
     * @return Configuration
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
