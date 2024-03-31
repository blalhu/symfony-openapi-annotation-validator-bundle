<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;

class HeaderNoticeErrorActionTest extends TestCase
{
    public function testImplementation(): void
    {
        $this->assertTrue(
            in_array(
                ErrorActionInterface::class,
                class_implements(new HeaderNoticeErrorAction())
            )
        );
        (new HeaderNoticeErrorAction())->triggerAction(new ValidationErrorException(new \Exception()));
    }
}
