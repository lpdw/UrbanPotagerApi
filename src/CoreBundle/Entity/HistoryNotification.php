<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoryNotification
 *
 * @ORM\Table(name="history_notification")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\HistoryNotificationRepository")
 */
class HistoryNotification
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sendAt", type="datetime")
     */
    private $sendAt;

    /**
     * @var \CoreBundle\Entity\Garden
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Garden")
     */
    private $garden;

    /**
     * @var \CoreBundle\Entity\Alert
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Alert")
     */
    private $alert;


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
     * Set sendAt
     *
     * @param \DateTime $sendAt
     *
     * @return HistoryNotification
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    /**
     * Get sendAt
     *
     * @return \DateTime
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * @return Garden
     */
    public function getGarden()
    {
        return $this->garden;
    }

    /**
     * @param Garden $garden
     *
     * @return HistoryNotification
     */
    public function setGarden($garden)
    {
        $this->garden = $garden;

        return $this;
    }

    /**
     * @return Alert
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * @param Alert $alert
     *
     * @return HistoryNotification
     */
    public function setAlert($alert)
    {
        $this->alert = $alert;

        return $this;
    }
}

