<?php

namespace CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use CoreBundle\Entity\Measure;

/**
 * Class MeasureSentEvent
 *
 * @package CoreBundle\Event
 */
class MeasureSentEvent extends Event
{
    const NAME = 'measure.sent';

    /**
     * @var Measure
     */
    protected $measure;

    public function __construct(Measure $measure)
    {
        $this->measure = $measure;
    }

    /**
     * @return Measure
     */
    public function getMeasure()
    {
        return $this->measure;
    }
}
