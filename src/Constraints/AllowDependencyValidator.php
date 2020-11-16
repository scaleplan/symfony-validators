<?php
declare(strict_types=1);

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
     * @param string $propertyName
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    protected function getRefPropertyValue(string $propertyName)
    {
        static $refPropertyValues = [];
        if (!isset($refPropertyValues[$propertyName])) {
            $object = $this->context->getObject();
            $refObject = new \ReflectionObject($object);
            $refProperty = $refObject->getProperty($propertyName);
            $changeAccessible = !$refProperty->isPublic();
            $changeAccessible && $refProperty->setAccessible(true);

            $refPropertyValues[$propertyName] = $refProperty->getValue($object);

            $changeAccessible && $refProperty->setAccessible(false);
        }

        return $refPropertyValues[$propertyName];
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @throws \ReflectionException
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
            if (method_exists($contextObject, $methodName)) {
                $depValue = $contextObject->{$methodName}();
            } else {
                $depValue = $this->getRefPropertyValue($dependency);
            }

            if ((!$constraint->reverse && ($depValue === null || (!$constraint->strict && $depValue === '')))
                || ($constraint->reverse && $depValue !== null && !$constraint->strict && $depValue !== '')
            ) {
                //$this->context->setNode($depValue, $contextObject, null, $dependency);
                $violations[] = $this->context
                    ->buildViolation(
                        $constraint->message
                        ?? 'Not null property "{{ filter }}" required '
                        . (!$constraint->reverse ? 'not ' : '')
                        . 'null property "{{ dependency }}".'
                    )
                    ->setParameter('{{ filter }}', $this->context->getPropertyPath())
                    ->setParameter('{{ dependency }}', $dependency);

                if ($constraint->all) {
                    break;
                }
            }
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
