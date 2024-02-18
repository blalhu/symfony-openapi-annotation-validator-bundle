<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorInterface;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorService;
use PHPUnit\Framework\TestCase;

class RequestInterceptorServiceTest extends TestCase
{
    public function testInterceptorImplementsInterface()
    {
        $this->assertTrue(
            in_array(
                RequestInterceptorInterface::class,
                class_implements(RequestInterceptorService::class)
            )
        );
    }
}
