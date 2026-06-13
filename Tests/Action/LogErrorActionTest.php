<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LogErrorActionTest extends TestCase
{
    public function testImplementation(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->assertTrue(
            in_array(
                ErrorActionInterface::class,
                class_implements(new LogErrorAction($logger))
            )
        );
        (new LogErrorAction($logger))->triggerAction(
            new ValidationErrorException(new \Exception()),
            $this->createMock(FilterControllerEvent::class)
        );
    }
}
