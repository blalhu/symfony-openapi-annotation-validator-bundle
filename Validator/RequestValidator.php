<?php

namespace Pelso\OpenAPIValidatorBundle\Validator;

use Pelso\OpenAPIValidatorBundle\Validator\RequestValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestValidator implements RequestValidatorInterface
{

    public function validate(Request $request): bool
    {
        // TODO: Implement validate() method.
        return true;
    }
}
