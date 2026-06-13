<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class HeaderNoticeErrorAction implements ErrorActionInterface
{
    public const HEADER_KEY = 'X_PELSO_OPENAPI_VALIDATION_ERROR';

    public function __construct()
    {
    }

    public function triggerAction(
        ValidationErrorException $exception,
        FilterControllerEvent $controllerEvent
    ): void {
        $controllerEvent->getRequest()->headers->set(
            self::HEADER_KEY,
            $exception->getPrevious()->getMessage()
        );
    }

    public static function getGeneratedHeaderKey(): string
    {
        return str_replace(
            '_',
            '-',
            strtolower(
                self::HEADER_KEY
            )
        );
    }
}
