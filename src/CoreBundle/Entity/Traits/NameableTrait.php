<?php

namespace CoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Class NameableTrait
 * @package CoreBundle\Entity\Traits
 *
 * @JMS\ExclusionPolicy("all")
 */
Trait NameableTrait
{
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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
