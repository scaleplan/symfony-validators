<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class AllowDependency
 *
 * @package Constraints
 *
 * @Annotation
 *
 * Validation constraint class for detect discrepancy properties in request templates objects
 * Example condition: If the object contains property "x"
 * then it must contains property "y", else it must pass of invalid
 */
class AllowDependency extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy() : string
    {
        return AllowDependencyValidator::class;
    }

    /**
     * @var array
     */
    public $dependencies;

    /**
     * @var string
     */
    public $message;

    /**
     * @return string
     */
    public function getDefaultOption() : string
    {
        return 'dependencies';
    }

    /**
     * @return array
     */
    public function getRequiredOptions() : array
    {
        return ['dependencies'];
    }
}
