<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Validator;

use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestValidatorTest extends TestCase
{

    public function testValidator(): void
    {
        $this->assertEquals(true, true);
    }
}
