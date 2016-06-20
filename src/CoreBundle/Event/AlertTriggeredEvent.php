<?php

namespace CoreBundle\Event;

use CoreBundle\Entity\Garden;
use Symfony\Component\EventDispatcher\Event;
use CoreBundle\Entity\Alert;

/**
 * Class AlertTriggeredEvent
 *
 * @package CoreBundle\Event
 */
class AlertTriggeredEvent extends Event
{
    const NAME = 'alert.triggered';

    /**
     * @var Alert
     */
    protected $alert;

    /**
     * @var Garden
     */
    protected $garden;

    public function __construct(Garden $garden, Alert $alert)
    {
        $this->garden = $garden;
        $this->alert = $alert;
    }

    /**
     * @return Alert
     */
    public function getAlert()
    {
        return $this->alert;
    }

    public function getGarden()
    {
        return $this->garden;
    }
}
