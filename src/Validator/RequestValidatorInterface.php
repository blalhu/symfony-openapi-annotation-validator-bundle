<?php

namespace Pelso\OpenAPIValidatorBundle\Validator;

use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestValidatorInterface
{
    public function validate(
        Request $request,
        OpenAPIProviderInterface $openAPIProvider
    ): void;
}
