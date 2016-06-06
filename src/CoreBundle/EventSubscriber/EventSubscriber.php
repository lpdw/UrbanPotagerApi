<?php

namespace CoreBundle\EventSubscriber;

use CoreBundle\Service\SendNotification;
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

    /**
     * @var \CoreBundle\Service\SendNotification
     */
    private $sendNotification;

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

    public function __construct(TriggerAlert $triggerAlert, SendNotification $sendNotification)
    {
        $this->triggerAlert = $triggerAlert;
        $this->sendNotification = $sendNotification;
    }

    public function measureSent(MeasureSentEvent $event)
    {
        $this->triggerAlert->triggerAlert($event->getMeasure());
    }

    public function alertTriggered(AlertTriggeredEvent $event)
    {
        $this->sendNotification->send($event->getGarden(), $event->getAlert());
    }
}
