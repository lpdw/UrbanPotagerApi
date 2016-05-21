<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use CoreBundle\Validator\Constraints as CoreAssert;
use CoreBundle\Entity\Traits\NameableTrait;

/**
 * Type
 *
 * @ORM\Table(name="type")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\TypeRepository")
 * @UniqueEntity("name")
 * @CoreAssert\MinMax
 * @JMS\ExclusionPolicy("all")
 */
class Type
{
    // Need change ? See constraint Range for $type
    const PLANT     = 0;
    const SENSOR    = 1;

    use TimestampableEntity;
    use NameableTrait;

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
     * @ORM\Column(type="text")
     * @Assert\NotNull(message="constraints.not_null")
     * @JMS\Expose()
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=2)
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
     * @JMS\Groups({"detail-type"})
     */
    private $min;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=2)
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
     * @JMS\Groups({"detail-type"})
     */
    private $max;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank(message="constraints.not_blank")
     * @Assert\Range(
     *      min = 0,
     *      max = 1,
     *      minMessage = "constraints.range.min",
     *      maxMessage = "constraints.range.max",
     * )
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
     * Set description
     *
     * @param string $description
     *
     * @return Type
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
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set min
     *
     * @param string $min
     *
     * @return Type
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return string
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     *
     * @param string $max
     *
     * @return Type
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return string
     */
    public function getMax()
    {
        return $this->max;
    }
}
