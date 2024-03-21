<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Validator;

use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorInterface;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestValidatorTest extends TestCase
{

    public function testValidator(): void
    {
        $this->assertEquals(
            false,
            (new RequestValidator())->validate(new Request())
        );
    }
}
