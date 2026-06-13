<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LogErrorAction implements ErrorActionInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function triggerAction(
        ValidationErrorException $exception,
        FilterControllerEvent $controllerEvent
    ): void {
        $this->logger->error(
            $exception->getPrevious()->getMessage(),
            [$exception]
        );
    }
}
