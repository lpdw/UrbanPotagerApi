<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="share")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ShareRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type_entity", type="string")
 * @ORM\DiscriminatorMap({
 *  "user_share" = "UserShare",
 * })
 */
abstract class Share
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank(message="constraints.not_blank")
     */
    protected $message;

    /**
     * @var garden
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Garden", inversedBy="shares")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $garden;

    /**
     * use by JMS (config/serializer/Entity.UserShare.yml)
     */
    public function garden()
    {
        return [
            'slug' => $this->garden->getSlug(),
            'name' => $this->garden->getName(),
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
     * Set message
     *
     * @param string $message
     *
     * @return Share
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
     * @return Share
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
