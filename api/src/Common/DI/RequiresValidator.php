<?php

namespace App\Common\DI;

use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Used to automatically inject the message bus to any service that needs it.
 */
trait RequiresValidator
{
    protected ?ValidatorInterface $validator = null;

    #[Required]
    public function withValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    protected function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
}
