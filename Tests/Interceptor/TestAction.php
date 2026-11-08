<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class TestAction implements ErrorActionInterface
{
    /** @var string $serviceName */
    private $serviceName;

    /** @var array $calledServiceNames */
    private $calledServiceNames;

    public function __construct(
        string $serviceName,
        array &$calledServiceNames
    ) {
        $this->serviceName = $serviceName;
        $this->calledServiceNames =& $calledServiceNames;
    }

    public function triggerAction(
        ValidationErrorException $exception,
        FilterControllerEvent $controllerEvent
    ): void {
        $this->calledServiceNames[] = $this->serviceName;
    }
}
