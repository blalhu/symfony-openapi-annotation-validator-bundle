<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ExceptionErrorAction implements ErrorActionInterface
{
    public function __construct()
    {
    }

    public function triggerAction(
        string $formattedErrorMessage,
        ValidationErrorException $exception,
        FilterControllerEvent $controllerEvent
    ): void {
        throw new ValidationErrorException(
            $exception->getPrevious(),
            $formattedErrorMessage
        );
    }
}
