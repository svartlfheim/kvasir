<?php

namespace App\Common\DI;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Used to automatically inject the message bus to any service that needs it.
 */
trait RequiresValidator
{
    protected ?ValidatorInterface $validator = null;

    #[Required]
    public function withValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    protected function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
}
