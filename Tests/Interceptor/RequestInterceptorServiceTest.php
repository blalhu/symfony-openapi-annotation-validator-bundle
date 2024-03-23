<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Action\BadRequestResponseErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\ExceptionErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\HeaderNoticeErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\LogErrorAction;
use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidatorAnnotation;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotation;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollection;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorInterface;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorService;
use Pelso\OpenAPIValidatorBundle\Tests\Annotation\ValidatorAnnotationChecker;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestInterceptorServiceTest extends TestCase
{
    private $interceptorService;

    private $controllerEvent;

    public function setUp()
    {
        $this->controllerEvent = $this->createMock(FilterControllerEvent::class);
        $this->controllerEvent->method('getRequest')->willReturn(new Request());
        $this->controllerEvent->method('isMasterRequest')->willReturn(true);

        $this->interceptorService = new RequestInterceptorService(
            new RequestValidator(),
            new OpenAPIProviderCollection(),
            [
                ValidatorAnnotation::class,
                DefaultValidatorAnnotation::class
            ],
            [
                '@pelso.openapi_validator_bundle.error_action.bad_request_response' => new BadRequestResponseErrorAction(),
                '@pelso.openapi_validator_bundle.error_action.exception' => new ExceptionErrorAction(),
                '@pelso.openapi_validator_bundle.error_action.header_notice' => new HeaderNoticeErrorAction(),
                '@pelso.openapi_validator_bundle.error_action.log' => new LogErrorAction(),
            ]
        );
    }

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
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn([new ValidatorAnnotationChecker(), 'defaultAction']);

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            'en',
            $controllerEvent->getRequest()->getLocale()
        );
    }
}
