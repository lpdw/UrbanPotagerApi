<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * MeasurePost
 *
 * @ORM\Table(name="measure_post")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\MeasurePostRepository")
 */
class MeasurePost extends Post
{

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
     * @var type
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Measure")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $type;


    /**
     * Set value
     *
     * @param float $value
     *
     * @return MeasurePost
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
     * @param \CoreBundle\Entity\Measure $type
     *
     * @return MeasurePost
     */
    public function setType(\CoreBundle\Entity\Measure $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \CoreBundle\Entity\Measure
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
     * @return MeasurePost
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
