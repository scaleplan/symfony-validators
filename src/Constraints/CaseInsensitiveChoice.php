<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotStrictChoice;

/**
 * Class CaseInsensitiveChoice
 *
 * @package Constraints
 *
 * @Annotation
 *
 * Constraint class for detect invalid format of sorting section in request template objects
 */
class CaseInsensitiveChoice extends NotStrictChoice
{
    /**
     * @return string
     */
    public function validatedBy() : string
    {
        return CaseInsensitiveChoiceValidator::class;
    }
}
