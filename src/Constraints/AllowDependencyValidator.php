<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class AllowDependencyValidator
 *
 * @package Scaleplan\Validator\Constraints
 *
 * Validator class for detect discrepancy properties in request templates objects
 * Example condition: If the object contains property "x"
 * then it must contains property "y", else it must pass of invalid
 */
class AllowDependencyValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof AllowDependency) {
            throw new UnexpectedTypeException($constraint, AllowDependency::class);
        }

        /** @var \object $contextObject */
        $contextObject = $this->context->getObject();

        foreach ($constraint->dependencies as $dependency) {
            $methodName = 'get' . ucfirst($dependency);
            if (!method_exists($contextObject, $methodName) || ($contextObject->{$methodName}() === null && $value)) {
                $this->context->setNode($value, $contextObject, null, $dependency);
                $this->context
                    ->buildViolation(
                        $constraint->message
                        ?? 'Not null property "{{ filter }}" required not null property {{{ dependency }}}.'
                    )
                    ->setParameter('{{ filter }}', $this->context->getPropertyName())
                    ->setParameter('{{ dependency }}', $dependency)
                    ->addViolation();
            }
        }
    }
}
