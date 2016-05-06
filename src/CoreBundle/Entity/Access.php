<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Access
 *
 * @ORM\Table(name="access")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\AccessRepository")
 * @UniqueEntity(fields={"garden", "type"})
 * @JMS\ExclusionPolicy("all")
 */
class Access
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Assert\Type(
     *     type="bool",
     *     message="constraints.type"
     * )
     * @JMS\Expose()
     */
    private $isPublic;

    /**
     * @var Garden
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Garden", inversedBy="access")
     */
    private $garden;

    /**
     * @var Type
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Type")
     * @ORM\JoinColumn(onDelete="SET NULL")
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
     * Set isPublic
     *
     * @param boolean $isPublic
     *
     * @return Access
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set garden
     *
     * @param \CoreBundle\Entity\Garden $garden
     *
     * @return Access
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
     * @return Access
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
}
