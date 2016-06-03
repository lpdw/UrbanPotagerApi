<?php

namespace CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MinMax extends Constraint
{
    public $message = 'constraints.min_max';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
