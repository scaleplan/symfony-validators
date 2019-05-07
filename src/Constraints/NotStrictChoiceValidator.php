<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * ChoiceValidator validates that the value is one of the expected values without type checking.
 */
class NotStrictChoiceValidator extends ChoiceValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof NotStrictChoice) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\NotStrictChoice');
        }

        if (!\is_array($constraint->choices) && !$constraint->callback) {
            throw new ConstraintDefinitionException(
                'Either "choices" or "callback" must be specified on constraint Choice'
            );
        }

        if (null === $value) {
            return;
        }

        if ($constraint->multiple && !\is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        if ($constraint->callback) {
            if (!\is_callable($choices = [$this->context->getObject(), $constraint->callback])
                && !\is_callable($choices = [$this->context->getClassName(), $constraint->callback])
                && !\is_callable($choices = $constraint->callback)
            ) {
                throw new ConstraintDefinitionException('The Choice constraint expects a valid callback');
            }
            $choices = $choices();
        } else {
            $choices = $constraint->choices;
        }

        if ($constraint->multiple) {
            foreach ($value as $_value) {
                if (!\in_array($_value, $choices, true)) {
                    $this->context->buildViolation($constraint->multipleMessage)
                        ->setParameter('{{ value }}', $this->formatValue($_value))
                        ->setCode(NotStrictChoice::NO_SUCH_CHOICE_ERROR)
                        ->setInvalidValue($_value)
                        ->addViolation();

                    return;
                }
            }

            $count = \count($value);

            if (null !== $constraint->min && $count < $constraint->min) {
                $this->context->buildViolation($constraint->minMessage)
                    ->setParameter('{{ limit }}', $constraint->min)
                    ->setPlural((int) $constraint->min)
                    ->setCode(NotStrictChoice::TOO_FEW_ERROR)
                    ->addViolation();

                return;
            }

            if (null !== $constraint->max && $count > $constraint->max) {
                $this->context->buildViolation($constraint->maxMessage)
                    ->setParameter('{{ limit }}', $constraint->max)
                    ->setPlural((int) $constraint->max)
                    ->setCode(NotStrictChoice::TOO_MANY_ERROR)
                    ->addViolation();

                return;
            }
        } elseif (!\in_array($value, $choices, false)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(NotStrictChoice::NO_SUCH_CHOICE_ERROR)
                ->addViolation();
        }
    }
}
