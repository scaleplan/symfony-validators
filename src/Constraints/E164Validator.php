<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class E164Validator
 * 
 * @package Scaleplan\Validator\Constraints
 */
class E164Validator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof E164) {
            throw new UnexpectedTypeException($constraint, E164::class);
        }

        if (null === $value) {
            return;
        }

        if (!preg_match('/^\+?[1-9]\d{1,14}$/', $value)) {
            $this->context->buildViolation('Value must be e164 standard phone number')->addViolation();
        }
    }
}
