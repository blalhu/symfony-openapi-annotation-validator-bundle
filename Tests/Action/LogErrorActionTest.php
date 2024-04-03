<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LogErrorActionTest extends TestCase
{
    public function testImplementation(): void
    {
        $this->assertTrue(
            in_array(
                ErrorActionInterface::class,
                class_implements(new LogErrorAction())
            )
        );
        (new LogErrorAction())->triggerAction(
            new ValidationErrorException(new \Exception()),
            $this->createMock(FilterControllerEvent::class)
        );
    }
}
