<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Choice;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class NotStrictChoice extends Choice
{
    /**
     * @return string
     */
    public function validatedBy() : string
    {
        return NotStrictChoiceValidator::class;
    }
}
