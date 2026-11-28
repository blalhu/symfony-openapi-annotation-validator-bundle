<?php

namespace Pelso\OpenAPIValidatorBundle\Formatter;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Pelso\OpenAPIValidatorBundle\Formatter\ValidationErrorExceptionMessageFormatterInterface;

class PassThroughErrorExceptionMessageFormatter implements ValidationErrorExceptionMessageFormatterInterface
{

    public function format(ValidationErrorException $exception): string
    {
        return $exception->getPrevious()->getMessage();
    }
}
