<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class IsInstanceOf
 *
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
    public function validatedBy() : string
    {
        return IsInstanceOfValidator::class;
    }
}
