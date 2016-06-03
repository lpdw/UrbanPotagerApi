<?php

namespace CoreBundle\Event;

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

    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * @return Alert
     */
    public function getAlert()
    {
        return $this->alert;
    }
}
