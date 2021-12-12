<?php

namespace App\Common\ArgumentResolver;

use App\Common\DTO\FromRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

// Hooked up in services.yaml
// See: https://symfony.com/doc/current/controller/argument_value_resolver.html#adding-a-custom-value-resolver
class DTOResolver implements ArgumentValueResolverInterface
{
    protected Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }
    /**
     * Whether this resolver can resolve the value for the given ArgumentMetadata.
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), FromRequest::class);
    }

    /**
     * Returns the possible value(s).
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dtoClass = $argument->getType();

        yield $dtoClass::fromRequest($this->request);
    }
}
