<?php

namespace CoreBundle\Service;

use CoreBundle\Entity\Measure;
use CoreBundle\Entity\Alert;
use CoreBundle\Event\AlertTriggeredEvent;

class TriggerAlert
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine, \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher)
    {
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Measure $measure
     */
    public function triggerAlert(Measure $measure)
    {
        /** @var \CoreBundle\Repository\AlertRepository $repo */
        $repo = $this->doctrine->getRepository('CoreBundle:Alert');

        $alerts = $repo->getAlertByGardenAndType($measure->getGarden(), $measure->getType());

        /** @var Alert $alert */
        foreach ($alerts as $alert) {
            if ($this->isTriggered($measure, $alert)) {
                $event = new AlertTriggeredEvent($measure->getGarden(), $alert);

                $this->dispatcher->dispatch(AlertTriggeredEvent::NAME, $event);
            }
        }
    }

    /**
     * @param Measure $measure
     * @param Alert $alert
     * @return bool
     */
    private function isTriggered(Measure $measure, Alert $alert)
    {
        $value = $measure->getValue();
        $threshold = $alert->getThreshold();
        $operator = Alert::$OPERATOR;

        switch ($alert->getComparison())
        {
            case $operator['equal']:
                return $value == $threshold;

            case $operator['not_equal']:
                return $value != $threshold;

            case $operator['less_than']:
                return $value < $threshold;

            case $operator['greater_than']:
                return $value > $threshold;

            case $operator['less_than_or_equal']:
                return $value <= $threshold;

            case $operator['greater_than_or_equal']:
                return $value >= $threshold;

            default:
                return false;
        }
    }
}
