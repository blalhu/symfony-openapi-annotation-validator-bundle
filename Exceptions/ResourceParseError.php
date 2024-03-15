<?php

namespace Pelso\OpenAPIValidatorBundle\Exceptions;

class ResourceParseError extends \ParseError
{
    public function __construct($message = "OpenAPI resource parse error!", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
