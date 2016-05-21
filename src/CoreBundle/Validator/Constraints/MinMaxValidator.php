<?php

namespace CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MinMaxValidator extends ConstraintValidator
{
    /**
     * @param \CoreBundle\Entity\Type $type
     * @param Constraint $constraint
     */
    public function validate($type, Constraint $constraint)
    {
        if ($type->getMin() >= $type->getMax()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
