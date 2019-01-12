<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class CaseInsensitiveChoiceValidator
 *
 * @package Scaleplan\Validator\Constraints
 *
 * Validator for detect invalid format of sorting section in request template objects
 */
class CaseInsensitiveChoiceValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CaseInsensitiveChoice) {
            throw new UnexpectedTypeException($constraint, CaseInsensitiveChoice::class);
        }

        if (!\is_array($constraint->choices)) {
            throw new ConstraintDefinitionException(
                'Either "choices" must be specified on constraint ' . CaseInsensitiveChoice::class
            );
        }

        if (null === $value) {
            return;
        }

        if ($constraint->multiple && !\is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (!\is_array($value)) {
            $value = [$value];
        }

        if (\array_uintersect($value, $constraint->choices, 'strcasecmp') !== $value) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(CaseInsensitiveChoice::NO_SUCH_CHOICE_ERROR)
                ->addViolation();
        }
    }
}
