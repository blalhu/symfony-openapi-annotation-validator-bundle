<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;

interface ErrorActionInterface
{
    public function triggerAction(ValidationErrorException $exception): void;
}
