<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Unit\Action;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;
use Pelso\OpenAPIValidatorBundle\Action\ExceptionErrorAction;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ExceptionErrorActionTest extends TestCase
{
    public function testImplementation(): void
    {
        $this->assertTrue(
            in_array(
                ErrorActionInterface::class,
                class_implements(new ExceptionErrorAction())
            )
        );
    }

    public function testExceptionThrown(): void
    {
        $this->expectException(ValidationErrorException::class);

        (new ExceptionErrorAction())->triggerAction(
            '',
            new ValidationErrorException(new \Exception()),
            $this->createMock(FilterControllerEvent::class)
        );
    }
}
