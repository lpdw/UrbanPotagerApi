<?php

namespace CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CoreBundle\Service\TriggerAlert;
use CoreBundle\Event\AlertTriggeredEvent;
use CoreBundle\Event\MeasureSentEvent;

class EventSubscriber implements EventSubscriberInterface
{
    /**
     * @var TriggerAlert
     */
    private $triggerAlert;

    public static function getSubscribedEvents()
    {
        return [
            MeasureSentEvent::NAME => [
                ['measureSent', 0],
            ],
            AlertTriggeredEvent::NAME => [
                ['alertTriggered', 0],
            ],
        ];
    }

    public function __construct(TriggerAlert $triggerAlert)
    {
        $this->triggerAlert = $triggerAlert;
    }

    public function measureSent(MeasureSentEvent $event)
    {
        $this->triggerAlert->triggerAlert($event->getMeasure());
    }

    public function alertTriggered(AlertTriggeredEvent $event)
    {
        // TODO use service for send alert
    }
}
