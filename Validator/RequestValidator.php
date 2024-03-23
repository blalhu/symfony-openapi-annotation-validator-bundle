<?php

namespace Pelso\OpenAPIValidatorBundle\Validator;

use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestValidator implements RequestValidatorInterface
{

    public function validate(
        Request $request,
        OpenAPIProviderInterface $openAPIProvider
    ): bool {
        return false;
    }
}
