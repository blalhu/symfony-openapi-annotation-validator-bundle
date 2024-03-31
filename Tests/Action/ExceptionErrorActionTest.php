<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;

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
        (new ExceptionErrorAction())->triggerAction(new ValidationErrorException(new \Exception()));
    }
}
