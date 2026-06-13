<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class BadRequestResponseErrorActionTest extends TestCase
{
    public function testImplementation(): void
    {
        $this->assertTrue(
            in_array(
                ErrorActionInterface::class,
                class_implements(new BadRequestResponseErrorAction())
            )
        );
    }

    public function testActionCall(): void
    {


        $errorController = new Class {
            private $called = false;

            public function badRequestAction(): void
            {
                $this->called = true;
            }

            public function methodCalled(): bool
            {
                return $this->called;
            }
        };

        $controller = function () {
        };

        $filterControllerEvent = $this->createMock(FilterControllerEvent::class);
        $filterControllerEvent->method('setController')->willReturnCallback(function ($callable) use (&$controller) {
            $controller = $callable;
        });

        (new BadRequestResponseErrorAction([$errorController, 'badRequestAction']))->triggerAction(
            new ValidationErrorException(new \Exception()),
            $filterControllerEvent
        );

        $controller();

        $this->assertTrue($errorController->methodCalled());
    }
}
