<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Type;

/**
 * Class FuzzyType
 *
 * @Annotation
 *
 * @package Scaleplan\Validator\Constraints
 */
class FuzzyType extends Type
{
    /**
     * @return string
     */
    public function validatedBy() : string
    {
        return FuzzyTypeValidator::class;
    }
}
