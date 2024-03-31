<?php

namespace Pelso\OpenAPIValidatorBundle\Exceptions;

class ValidationErrorException extends \Exception
{
    public function __construct(
        \Throwable $previous,
        $message = "",
        $code = 0
    ) {
        parent::__construct($message, $code, $previous);
    }
}
