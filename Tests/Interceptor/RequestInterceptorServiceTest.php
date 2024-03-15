<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorInterface;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Tests\Command\CacheClearCommand\Fixture\TestAppKernel;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\app\AppKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;

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

    public function testEventHandler(): void
    {
        $filterControllerEvent = new FilterControllerEvent(
            new TestAppKernel('test', false),
            function () {
            },
            new Request(),
            1
        );
        $service = new RequestInterceptorService();
        $service->onKernelController($filterControllerEvent);
        $this->assertEquals(
            'test_value',
            $filterControllerEvent->getRequest()->getLocale()
        );
    }
}
