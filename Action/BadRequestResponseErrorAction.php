<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Controller\BadRequestResponseController;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class BadRequestResponseErrorAction implements ErrorActionInterface
{
    private $badRequestAction;

    public function __construct(callable $badRequestAction = null)
    {
        if (null === $badRequestAction) {
            $badRequestAction = [new BadRequestResponseController(), 'badRequestAction'];
        }

        $this->badRequestAction = $badRequestAction;
    }

    public function triggerAction(
        ValidationErrorException $exception,
        FilterControllerEvent $controllerEvent
    ): void {
        $controllerEvent->setController(function () use ($exception) {
            return call_user_func_array(
                $this->badRequestAction,
                [$exception]
            );
        });
    }
}
