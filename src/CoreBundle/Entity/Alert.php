<?php

namespace CoreBundle\Entity;

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
class Alert
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
     * @JMS\Groups({"detail-alert"})
     */
    private $message;

    /**
     * @var \CoreBundle\Entity\Garden
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Garden")
     * @JMS\Expose()
     * @JMS\Groups({"detail-alert"})
     */
    private $garden;

    /**
     * @var \CoreBundle\Entity\Type
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Type")
     * @JMS\Expose()
     */
    private $type;


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
     * Set garden
     *
     * @param \CoreBundle\Entity\Garden $garden
     *
     * @return Alert
     */
    public function setGarden(\CoreBundle\Entity\Garden $garden = null)
    {
        $this->garden = $garden;

        return $this;
    }

    /**
     * Get garden
     *
     * @return \CoreBundle\Entity\Garden
     */
    public function getGarden()
    {
        return $this->garden;
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
}
