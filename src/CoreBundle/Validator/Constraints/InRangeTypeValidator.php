<?php

namespace CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InRangeTypeValidator extends ConstraintValidator
{
    /**
     * @param \CoreBundle\Entity\Alert $alert
     * @param Constraint $constraint
     */
    public function validate($alert, Constraint $constraint)
    {
        $type = $alert->getType();

        if (is_null($type)) {
            return; // avoid 500, constraint handle by constraint on attribut $type
        }

        $min = $alert->getType()->getMin();
        $max = $alert->getType()->getMax();

        if ($alert->getThreshold() > $max) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ limit }}', $max)
                ->addViolation();
        }

        if ($alert->getThreshold() < $min) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ limit }}', $min)
                ->addViolation();
        }
    }
}
