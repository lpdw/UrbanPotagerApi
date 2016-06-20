<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CoreBundle\Entity\Traits\LocalizableTrait;
use CoreBundle\Entity\Traits\AddressableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use CoreBundle\Entity\Interfaces\OwnableInterface;
use CoreBundle\Entity\Traits\NameableTrait;

/**
 * Garden
 *
 * @ORM\Table(name="garden")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\GardenRepository")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 */
class Garden implements OwnableInterface
{
    use LocalizableTrait;
    use AddressableTrait;
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
     * @ORM\Column(type="guid")
     * @Assert\Uuid(message="constraints.uuid")
     * @JMS\Expose()
     * @JMS\Groups({"me-garden"})
     */
    private $apiKey;

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
     * @JMS\Expose()
     * @JMS\Groups({"detail-garden"})
     */
    private $owner;

    /**
     * @var \CoreBundle\Entity\Configuration
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Configuration")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $configuration;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\Access", mappedBy="garden")
     */
    private $access;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="CoreBundle\Entity\Alert", mappedBy="gardens")
     */
    private $alerts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\Share", mappedBy="garden")
     */
    private $shares;

    /**
     * @JMS\VirtualProperty()
     * @JMS\Groups({"detail-garden"})
     */
    public function address()
    {
        if (!$this->showLocation) {
            return null;
        }

        return [
            'country' => $this->country,
            'zipCode' => $this->zipCode,
            'city' => $this->city,
            'line1' => $this->address1,
            'line2' => $this->address2,
        ];
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->access = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add access
     *
     * @param \CoreBundle\Entity\Access $access
     *
     * @return Garden
     */
    public function addAccess(\CoreBundle\Entity\Access $access)
    {
        $this->access[] = $access;

        return $this;
    }

    /**
     * Remove access
     *
     * @param \CoreBundle\Entity\Access $access
     */
    public function removeAccess(\CoreBundle\Entity\Access $access)
    {
        $this->access->removeElement($access);
    }

    /**
     * Get access
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Add alert
     *
     * @param \CoreBundle\Entity\Alert $alert
     *
     * @return Garden
     */
    public function addAlert(\CoreBundle\Entity\Alert $alert)
    {
        $this->alerts[] = $alert;

        return $this;
    }

    /**
     * Remove alert
     *
     * @param \CoreBundle\Entity\Alert $alert
     */
    public function removeAlert(\CoreBundle\Entity\Alert $alert)
    {
        $this->alerts->removeElement($alert);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Add share
     *
     * @param \CoreBundle\Entity\Share $share
     *
     * @return Garden
     */
    public function addShare(\CoreBundle\Entity\Share $share)
    {
        $this->shares[] = $share;

        return $this;
    }

    /**
     * Remove share
     *
     * @param \CoreBundle\Entity\Share $share
     */
    public function removeShare(\CoreBundle\Entity\Share $share)
    {
        $this->shares->removeElement($share);
    }

    /**
     * Get shares
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShares()
    {
        return $this->shares;
    }
}
