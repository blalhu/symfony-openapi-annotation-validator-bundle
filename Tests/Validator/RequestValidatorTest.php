<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Validator;

use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorInterface;
use Pelso\OpenAPIValidatorBundle\Provider\AbstractOpenAPIProvider;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidator;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestValidatorTest extends TestCase
{

    public function testValidator(): void
    {
        $this->assertEquals(
            false,
            (new RequestValidator())->validate(
                new Request(),
                $this->createMock(AbstractOpenAPIProvider::class)
            )
        );
    }
}
