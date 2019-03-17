<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class E164
 *
 * @package Constraints
 *
 * @Annotation
 */
class E164 extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy() : string
    {
        return E164Validator::class;
    }
}
