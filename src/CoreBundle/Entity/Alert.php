<?php

namespace CoreBundle\Entity;

use CoreBundle\Entity\Interfaces\OwnableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use CoreBundle\Entity\Traits\NameableTrait;
use CoreBundle\Validator\Constraints as CoreAssert;

/**
 * Alert
 *
 * @ORM\Table(name="alert")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\AlertRepository")
 * @CoreAssert\InRangeType
 * @JMS\ExclusionPolicy("all")
 */
class Alert implements OwnableInterface
{
    public static $OPERATOR = [
        'equal'                 => 1,
        'not_equal'             => 2,
        'less_than'             => 3,
        'greater_than'          => 4,
        'less_than_or_equal'    => 5,
        'greater_than_or_equal' => 6,
    ];

    use TimestampableEntity;
    use NameableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="threshold", type="float")
     * @Assert\NotNull(message="constraints.not_null")
     * @Assert\Type(
     *     type="float",
     *     message="constraints.type"
     * )
     * @Assert\Range(
     *      min = -9999.99,
     *      max = 9999.99,
     *      minMessage = "constraints.range.min",
     *      maxMessage = "constraints.range.max",
     * )
     * @JMS\Expose()
     */
    private $threshold;

    /**
     * @var integer
     *
     * @ORM\Column(name="comparison", type="integer")
     * @Assert\NotNull(message="constraints.not_null")
     * @Assert\Type(
     *     type="integer",
     *     message="constraints.type"
     * )
     * @Assert\Range(
     *      min = 1,
     *      max = 6,
     *      minMessage = "constraints.range.min",
     *      maxMessage = "constraints.range.max",
     * )
     * @JMS\Expose()
     */
    private $comparison;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotNull(message="constraints.not_null")
     * @JMS\Expose()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     * @JMS\Expose()
     * @Assert\NotBlank(message="constraints.not_blank")
     * @Assert\Length(max=255, maxMessage="constraints.length.max")
     * @JMS\Expose()
     */
    private $message;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="CoreBundle\Entity\Garden", inversedBy="alerts")
     * @JMS\Expose()
     * @JMS\Groups({"detail-alert"})
     */
    private $gardens;

    /**
     * @var \CoreBundle\Entity\Type
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Type")
     * @Assert\NotNull(message="constraints.not_null")
     * @JMS\Expose()
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $type;

    /**
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @JMS\Expose()
     * @JMS\Groups({"detail-alert"})
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
     * Set threshold
     *
     * @param float $threshold
     *
     * @return Alert
     */
    public function setThreshold($threshold)
    {
        $this->threshold = $threshold;

        return $this;
    }

    /**
     * Get threshold
     *
     * @return float
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Alert
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
     * Set message
     *
     * @param string $message
     *
     * @return Alert
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set type
     *
     * @param \CoreBundle\Entity\Type $type
     *
     * @return Alert
     */
    public function setType(\CoreBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \CoreBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getComparison()
    {
        return $this->comparison;
    }

    /**
     * @param int $comparison
     *
     * @return Alert
     */
    public function setComparison($comparison)
    {
        $this->comparison = $comparison;

        return $this;
    }

    /**
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     *
     * @return Alert
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gardens = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add garden
     *
     * @param \CoreBundle\Entity\Garden $garden
     *
     * @return Alert
     */
    public function addGarden(\CoreBundle\Entity\Garden $garden)
    {
        $this->gardens[] = $garden;

        return $this;
    }

    /**
     * Remove garden
     *
     * @param \CoreBundle\Entity\Garden $garden
     */
    public function removeGarden(\CoreBundle\Entity\Garden $garden)
    {
        $this->gardens->removeElement($garden);
    }

    /**
     * Get gardens
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGardens()
    {
        return $this->gardens;
    }
}
