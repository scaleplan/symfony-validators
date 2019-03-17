<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Choice;

/**
 * Class CaseInsensitiveChoice
 *
 * @package Constraints
 *
 * @Annotation
 *
 * Constraint class for detect invalid format of sorting section in request template objects
 */
class CaseInsensitiveChoice extends Choice
{
    /**
     * @return string
     */
    public function validatedBy() : string
    {
        return CaseInsensitiveChoiceValidator::class;
    }
}
