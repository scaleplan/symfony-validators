<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class FuzzyType
 *
 * @package Scaleplan\Validator\Constraints
 */
class FuzzyType extends Constraint
{
    const INVALID_TYPE_ERROR = 'ba785a8c-82cb-4283-967c-3cf342181b40';

    protected static $errorNames = array(
        self::INVALID_TYPE_ERROR => 'INVALID_TYPE_ERROR',
    );

    public $message = 'This value should be of type {{ type }}.';
    public $type;

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'type';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return array('type');
    }
}
