<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class IsInstanceOfValidator
 *
 * @package Scaleplan\Validator\Constraints
 */
class IsInstanceOfValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof IsInstanceOf) {
            throw new UnexpectedTypeException($constraint, IsInstanceOf::class);
        }

        if (!$value instanceof $constraint->classname) {
            $this->context->buildViolation('Element must be instance of class {{ classname }}')
                ->setParameter('{{ classname }}', $constraint->classname)
                ->addViolation();
        }
    }
}
