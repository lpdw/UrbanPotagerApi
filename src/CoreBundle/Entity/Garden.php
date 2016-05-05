<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CoreBundle\Entity\Traits\LocalizableTrait;
use CoreBundle\Entity\Traits\AddressableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * Garden
 *
 * @ORM\Table(name="garden")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\GardenRepository")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 */
class Garden
{
    use LocalizableTrait;
    use AddressableTrait;
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
     * @ORM\Column(type="guid")
     * @Assert\Uuid(message="constraints.uuid")
     * @JMS\Expose()
     */
    private $apiKey;

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
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     * @JMS\Expose()
     */
    private $owner;

    /**
     * @var \CoreBundle\Entity\Configuration
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Configuration")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     * @JMS\Expose()
     */
    private $configuration;


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
     * @return Garden
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
     * @return Garden
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
     * Set isPublic
     *
     * @param boolean $isPublic
     *
     * @return Garden
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
     * Get showLocation
     *
     * @return boolean
     */
    public function getShowLocation()
    {
        return $this->showLocation;
    }

    /**
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     *
     * @return Garden
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
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return Garden
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
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
     * @return Garden
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function generateGuid()
    {
        $this->apiKey = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Garden
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Garden
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Garden
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set address1
     *
     * @param string $address1
     *
     * @return Garden
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     *
     * @return Garden
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set configuration
     *
     * @param \CoreBundle\Entity\Configuration $configuration
     *
     * @return Garden
     */
    public function setConfiguration(\CoreBundle\Entity\Configuration $configuration = null)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Get configuration
     *
     * @return \CoreBundle\Entity\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
