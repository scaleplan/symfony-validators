<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

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
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof AllowDependency) {
            throw new UnexpectedTypeException($constraint, AllowDependency::class);
        }

        /** @var object $contextObject */
        $contextObject = $this->context->getObject();

        if ($value === null || (!$constraint->strict && $value === '')) {
            return;
        }

        $violations = [];
        foreach ($constraint->dependencies as $dependency) {
            $methodName = 'get' . ucfirst($dependency);
            if (!$constraint->strict) {
                if ((!$constraint->reverse
                        && (!method_exists($contextObject, $methodName)
                            || $contextObject->{$methodName}() === null || $contextObject->{$methodName}() === ''))
                    || ($constraint->reverse
                        && (method_exists($contextObject, $methodName)
                            && ($contextObject->{$methodName}() !== null && $contextObject->{$methodName}() !== '')))) {
                    $this->context->setNode($value, $contextObject, null, $dependency);
                    $violations[] = $this->context
                        ->buildViolation(
                            $constraint->message
                            ?? 'Not null property "{{ filter }}" required not null property {{{ dependency }}}.'
                        )
                        ->setParameter('{{ filter }}', $this->context->getPropertyPath())
                        ->setParameter('{{ dependency }}', $dependency);
                }
                return;
            }

            if ($constraint->strict) {
                if ((!$constraint->reverse
                        && (!method_exists($contextObject, $methodName) || $contextObject->{$methodName}() === null))
                    || ($constraint->reverse
                        && (method_exists($contextObject, $methodName) && $contextObject->{$methodName}() !== null))) {

                    $this->context->setNode($value, $contextObject, null, $dependency);
                    $violations[] = $this->context
                        ->buildViolation(
                            $constraint->message
                            ?? ($constraint->reverse
                                ? 'Not null property "{{ filter }}" required null property {{{ dependency }}}.'
                                : 'Not null property "{{ filter }}" required not null property {{{ dependency }}}.')
                        )
                        ->setParameter('{{ filter }}', $this->context->getPropertyPath())
                        ->setParameter('{{ dependency }}', $dependency);
                }
                return;
            }

            if (!$constraint->all && count($constraint->dependencies) > count($violations)) {
                return;
            }

            /** @var ConstraintViolationBuilderInterface $violation */
            foreach ($violations as $violation) {
                $violation->addViolation();
            }
        }
    }
}
