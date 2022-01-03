<?php

namespace App\Common\Command;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\Error\Violation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommandValidator implements CommandValidatorInterface
{
    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(object $cmd): FieldValidationErrorList
    {
        $errors = $this->validator->validate($cmd);

        if (empty($errors)) {
            return FieldValidationErrorList::empty();
        }

        $fieldErrors = FieldValidationErrorList::empty();

        foreach ($this->violationsByProperty($errors) as $field => $violations) {
            $fieldErrors->add(FieldValidationError::new(
                $field,
                $violations
            ));
        }

        return $fieldErrors;
    }

    protected function violationsByProperty(ConstraintViolationList $errors): array
    {
        $violationsByProperty = [];

        foreach ($errors as $error) {
            $violationsByProperty[$error->getPropertyPath()][] = new Violation(
                $error->getMessage(),
                $this->baseName($error->getConstraint()),
            );
        }

        return $violationsByProperty;
    }

    protected function baseName($value): string
    {
        if (!is_object($value)) {
            return strtolower(gettype($value));
        }

        $class = str_replace('\\', "/", get_class($value));

        return strtolower(basename($class));
    }
}
