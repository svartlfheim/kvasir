<?php

namespace App\Common\Command;

use App\Common\API\Error\FieldValidationErrorList;

interface CommandValidatorInterface
{
    public function validate(object $cmd): FieldValidationErrorList;
}
