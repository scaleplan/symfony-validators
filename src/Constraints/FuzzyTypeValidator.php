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
     *
     * @throws \ReflectionException
     */
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof FuzzyType) {
            throw new UnexpectedTypeException($constraint, FuzzyType::class);
        }

        if (/*$constraint->type !== 'string' && */$value === '') {
            $value = null;
            $this->setValue($value);
        }

        if (null === $value) {
            return;
        }

        $type = strtolower($constraint->type);
        $type = 'boolean' === $type ? 'bool' : $constraint->type;
        $isFunction = 'is_' . $type;
        $ctypeFunction = 'ctype_' . $type;

        if (\function_exists($isFunction) && $isFunction($value)) {
            return;
        }

        if (\function_exists($ctypeFunction) && $ctypeFunction($value)) {
            return;
        }

        if ($value instanceof $constraint->type) {
            return;
        }

        if ($type === 'float' && false !== \filter_var($value, FILTER_VALIDATE_FLOAT)) {
            settype($value, $type);
            $this->setValue($value);
            return;
        }

        if ($type === 'bool') {
            if ($value === 'true') {
                $value = true;
                $this->setValue($value);
                return;
            }

            if ($value === 'false') {
                $value = false;
                $this->setValue($value);
                return;
            }
        }

        $tmpType = gettype($value);
        $tmp = $value;
        settype($tmp, $type);
        settype($tmp, $tmpType);
        if ($tmp === $value) {
            settype($value, $type);
            $this->setValue($value);
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatValue($value))
            ->setParameter('{{ type }}', $constraint->type)
            ->setCode(FuzzyType::INVALID_TYPE_ERROR)
            ->addViolation();
    }

    /**
     * @param string $propertyName
     *
     * @return \ReflectionProperty
     *
     * @throws \ReflectionException
     */
    protected function getRefProperty(string $propertyName) : \ReflectionProperty
    {
        static $refProperty = [];
        if (!isset($refProperty[$propertyName])) {
            $object = $this->context->getObject();
            $refObject = new \ReflectionObject($object);
            $refProperty[$propertyName] = $refObject->getProperty($propertyName);
            $refProperty[$propertyName]->setAccessible(true);
        }

        return $refProperty[$propertyName];
    }

    /**
     * @param $value
     *
     * @throws \ReflectionException
     */
    protected function setValue($value) : void
    {
        $object = $this->context->getObject();
        $propertyName = $this->context->getPropertyPath();
        if (preg_match('/(\w+)\[(\d+)\]$/', $propertyName, $matches)) {
            $index = (int)$matches[2];
            $propertyName = $matches[1];
            $value = [$index => $value,] + $this->getRefProperty($propertyName)->getValue($object);
        }

        $methodName = 'set' . ucfirst($propertyName);
        if ($object && property_exists($object, $propertyName)) {
            if (method_exists($object, $methodName)) {
                $object->{$methodName}($value);
            }

            $this->getRefProperty($propertyName)->setValue($object, $value);
        }
    }
}
