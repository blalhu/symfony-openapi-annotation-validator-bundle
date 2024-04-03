<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LogErrorAction implements ErrorActionInterface
{
    public function __construct()
    {
    }

    public function triggerAction(
        ValidationErrorException $exception,
        FilterControllerEvent $controllerEvent
    ): void {
        // TODO: Implement triggerAction() method.
    }
}
