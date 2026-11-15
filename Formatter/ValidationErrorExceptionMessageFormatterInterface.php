<?php

namespace Pelso\OpenAPIValidatorBundle\Formatter;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;

interface ValidationErrorExceptionMessageFormatterInterface
{
    public function format(ValidationErrorException $exception): string;
}
