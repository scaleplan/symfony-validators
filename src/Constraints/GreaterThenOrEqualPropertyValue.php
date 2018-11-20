<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class GreaterThenOrEqualPropertyValue
 *
 * @package AppBundle\Validator\Constraints
 *
 * @Annotation
 *
 * Check current property value greater than of value of property that name indicates in parameter
 */
class GreaterThenOrEqualPropertyValue extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy()
    {
        return GreaterThanOrEqualPropertyValueValidator::class;
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
