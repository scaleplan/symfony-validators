<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class LessThanOrEqualPropertyValueValidator
 *
 * @package AppBundle\Validator\Constraints
 *
 * Check current property value less than of value of property that name indicates in parameter
 */
class LessThanOrEqualPropertyValueValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof LessThenOrEqualPropertyValue) {
            throw new UnexpectedTypeException($constraint, LessThenOrEqualPropertyValue::class);
        }

        $propertyName = $constraint->property;

        if (!$propertyName || $value === null) {
            return;
        }

        $contextObject = $this->context->getObject();
        $methodName = 'get' . ucfirst($propertyName);
        if (!method_exists($contextObject, $methodName)) {
            $this->context->buildViolation(
                'Property "{{ compared_property }}" unavailable.'
            )
                ->setParameter('{{ compared_property }}', $propertyName)
                ->addViolation();
        }

        $compareValues = function ($propertyValue, $value) use ($constraint) {
            if ($propertyValue < $value) {
                $this->context->buildViolation(
                    $constraint->message
                    ?? 'Value of property "{{ checking }}" '
                        . 'should be greater than or equal to value of property "{{ compared_property }}".'
                )
                    ->setParameter('{{ checking }}', $this->context->getPropertyName())
                    ->setParameter('{{ compared_property }}', $constraint->property)
                    ->addViolation();
            }
        };

        $propertyValue = $contextObject->{$methodName}();
        if ($propertyValue === null) {
            return;
        }

        if ($constraint->type && $constraint->type !== 'date') {
            if (!settype($value, $constraint->type)) {
                $this->context->buildViolation(
                    'Value {{ value }} is not cast to type {{ type }}.'
                )
                    ->setParameter('{{ value }}', $value)
                    ->setParameter('{{ type }}', $constraint->type)
                    ->addViolation();
            }

            if (!settype($propertyValue, $constraint->type)) {
                $this->context->buildViolation(
                    'Value {{ value }} is not cast to type {{ type }}.'
                )
                    ->setParameter('{{ value }}', $propertyValue)
                    ->setParameter('{{ type }}', $constraint->type)
                    ->addViolation();
            }

            $compareValues($propertyValue, $value);
            return;
        }

        if ($constraint->type && $constraint->type === 'date') {
            try {
                $valueDate = new \DateTime($value);
            } catch (\Exception $e) {
                $this->context->buildViolation((new Date())->message)->addViolation();
                return;
            }

            try {
                $propertyValueDate = new \DateTime($propertyValue);
            } catch (\Exception $e) {
                return;
            }

            $compareValues($propertyValueDate, $valueDate);
            return;
        }
    }
}
