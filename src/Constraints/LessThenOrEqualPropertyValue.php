<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class LessThenOrEqualPropertyValue
 *
 * @package AppBundle\Validator\Constraints
 *
 * @Annotation
 *
 * Check current property value less than of value of property that name indicates in parameter
 */
class LessThenOrEqualPropertyValue extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy()
    {
        return LessThanOrEqualPropertyValueValidator::class;
    }

    /**
     * @var array
     */
    public $type;

    /**
     * @var array
     */
    public $property;

    /**
     * @var string
     */
    public $message;

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'property';
    }
}
