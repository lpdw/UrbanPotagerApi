<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Measure
 *
 * @ORM\Table(name="measure")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\MeasureRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Measure
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="constraints.not_blank")
     * @JMS\Expose()
     */
    private $value;

    /**
     * @var Type
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Type")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @JMS\Expose()
     */
    private $type;

    /**
     * @var Garden
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Garden")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @JMS\Expose()
     * @JMS\Groups({"detail-measure"})
     */
    private $garden;


    /**
     * @return \DateTime
     *
     * @JMS\VirtualProperty()
     */
    public function createdAt()
    {
        return $this->createdAt;
    }


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
     * Set value
     *
     * @param float $value
     *
     * @return Measure
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param \CoreBundle\Entity\Type $type
     *
     * @return Measure
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
     * Set garden
     *
     * @param \CoreBundle\Entity\Garden $garden
     *
     * @return Measure
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
}
