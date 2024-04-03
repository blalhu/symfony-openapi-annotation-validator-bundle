<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

interface ErrorActionInterface
{
    public function triggerAction(
        ValidationErrorException $exception,
        FilterControllerEvent $controllerEvent
    ): void;
}
