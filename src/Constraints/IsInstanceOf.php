<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CurrencyCode
 * @package Constraints
 *
 * @Annotation
 */
class IsInstanceOf extends Constraint
{
    /**
     * @var string
     */
    public $classname;

    /**
     * @return string
     */
    public function validatedBy()
    {
        return IsInstanceOfValidator::class;
    }
}
