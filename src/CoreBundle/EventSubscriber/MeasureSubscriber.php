<?php

namespace CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CoreBundle\Event\MeasureSentEvent;

class MeasureSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            MeasureSentEvent::NAME => [
                ['measureSent', 0],
            ]
        ];
    }

    public function measureSent(MeasureSentEvent $event)
    {
        // TODO use service for detect
    }
}
