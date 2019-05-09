<?php

namespace Scaleplan\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\TypeValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class FuzzyTypeValidator
 *
 * @package Scaleplan\Validator\Constraints
 */
class FuzzyTypeValidator extends TypeValidator
{
    /**
     * @param $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof FuzzyType) {
            throw new UnexpectedTypeException($constraint, FuzzyType::class);
        }

        if (null === $value) {
            return;
        }

        $type = strtolower($constraint->type);
        $type = 'boolean' === $type ? 'bool' : $constraint->type;
        $isFunction = 'is_'.$type;
        $ctypeFunction = 'ctype_'.$type;

        if (\function_exists($isFunction) && $isFunction($value)) {
            return;
        }

        if (\function_exists($ctypeFunction) && $ctypeFunction($value)) {
            return;
        }

        if ($value instanceof $constraint->type) {
            return;
        }

        if ($type === 'float') {
            if (false !== \filter_var($value, FILTER_VALIDATE_FLOAT)) {
                settype($value, $type);
                return;
            }
        } else {
            $tmpType = gettype($value);
            $tmp = $value;
            settype($tmp, $type);
            settype($tmp, $tmpType);
            if ($tmp === $value) {
                settype($value, $type);
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatValue($value))
            ->setParameter('{{ type }}', $constraint->type)
            ->setCode(FuzzyType::INVALID_TYPE_ERROR)
            ->addViolation();
    }
}
