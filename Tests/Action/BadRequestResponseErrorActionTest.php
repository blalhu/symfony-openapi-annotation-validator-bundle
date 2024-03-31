<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;

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
        (new BadRequestResponseErrorAction())->triggerAction(new ValidationErrorException(new \Exception()));
    }
}
