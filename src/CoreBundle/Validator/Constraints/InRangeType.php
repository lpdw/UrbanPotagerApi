<?php

namespace CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class InRangeType extends Constraint
{
    public $minMessage = 'constraints.range_type.min';
    public $maxMessage = 'constraints.range_type.max';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
