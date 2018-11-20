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
    public function validatedBy()
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
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'dependencies';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['dependencies'];
    }
}
