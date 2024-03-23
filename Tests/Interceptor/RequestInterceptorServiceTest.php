<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Action\BadRequestResponseErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\ExceptionErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\HeaderNoticeErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\LogErrorAction;
use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidatorAnnotation;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotation;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollection;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingErrorActionException;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorInterface;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorService;
use Pelso\OpenAPIValidatorBundle\Provider\AbstractOpenAPIProvider;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestInterceptorServiceTest extends TestCase
{
    private $interceptorService;

    private $controllerEvent;

    private $providerCollection;

    private $validator;

    private $providerMock;

    public function setUp()
    {
        $this->controllerEvent = $this->createMock(FilterControllerEvent::class);
        $this->controllerEvent->method('getRequest')->willReturn(new Request());
        $this->controllerEvent->method('isMasterRequest')->willReturn(true);

        $this->providerCollection = $this->createMock(OpenAPIProviderCollection::class);

        $this->validator = $this->createMock(RequestValidator::class);

        $this->interceptorService = new RequestInterceptorService(
            $this->validator,
            $this->providerCollection,
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

        $this->providerMock = $this->createMock(AbstractOpenAPIProvider::class);
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

    public function testClassAnnotation(): void
    {
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn([
            new ClassAnnotationController(),
            'defaultAction'
        ]);
        $providerName = '';
        $this->providerCollection->method('get')->willReturnCallback(function (string $name) use (&$providerName) {
            $providerName = $name;
            return $this->providerMock;
        });

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            'class',
            $providerName
        );
    }

    public function testMethodAnnotation(): void
    {
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn([
            new MethodAnnotationController(),
            'defaultAction'
        ]);
        $providerName = '';
        $this->providerCollection->method('get')->willReturnCallback(function (string $name) use (&$providerName) {
            $providerName = $name;
            return $this->providerMock;
        });

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            'method',
            $providerName
        );
    }

    public function testOverridingMethodAnnotation(): void
    {
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn([
            new ClassAndMethodAnnotationController(),
            'defaultAction'
        ]);
        $providerName = '';
        $this->providerCollection->method('get')->willReturnCallback(function (string $name) use (&$providerName) {
            $providerName = $name;
            return $this->providerMock;
        });

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            'method',
            $providerName
        );
    }

    public function testNoAnnotation(): void
    {
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn([
            new NoAnnotationController(),
            'defaultAction'
        ]);
        $validateCalled = false;
        $this->validator->method('validate')->willReturnCallback(function (string $name) use (&$validateCalled) {
            $validateCalled = true;
            return false;
        });

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            false,
            $validateCalled
        );
    }

    public function testNonExistingAnnotation(): void
    {
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn([
            new NonExistingAnnotationController(),
            'defaultAction'
        ]);
        $validateCalled = false;
        $this->validator->method('validate')->willReturnCallback(function (string $name) use (&$validateCalled) {
            $validateCalled = true;
            return false;
        });

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            false,
            $validateCalled
        );
    }

    public function testNonExistingErrorActionAnnotation(): void
    {
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn([
            new NonExistingErrorActionAnnotationController(),
            'defaultAction'
        ]);
        $this->validator->method('validate')->willReturnCallback(function (string $name) {
            return false;
        });

        $this->expectException(NonExistingErrorActionException::class);
        $this->interceptorService->onKernelController($controllerEvent);
    }

    public function testDefaultAnnotation(): void
    {
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn([
            new DefaultAnnotationController(),
            'defaultAction'
        ]);
        $this->validator->method('validate')->willReturnCallback(function (string $name) {
            return true;
        });
        $providerCollectionCalled = false;
        $this->providerCollection->method('get')->willReturnCallback(function (string $name) use (&$providerCollectionCalled) {
            $this->providerCollection = true;
            return $this->providerMock;
        });

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            false,
            $providerCollectionCalled
        );
    }



    public function testNonMasterRequest(): void
    {
        $controllerEvent = $this->createMock(FilterControllerEvent::class);
        $controllerEvent->method('getRequest')->willReturn(new Request());
        $controllerEvent->method('isMasterRequest')->willReturn(false);
        $getRequestCalled = false;
        $controllerEvent->method('getController')->willReturnCallback(function () use (&$getRequestCalled) {
            $getRequestCalled = true;
        });

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            false,
            $getRequestCalled
        );
    }



    public function testInvalidController(): void
    {
        $controllerEvent = clone $this->controllerEvent;
        $controllerEvent->method('getController')->willReturn(false);
        $validatorCalled = false;
        $this->validator->method('validate')->willReturnCallback(function (string $name) use (&$validatorCalled) {
            $validatorCalled = true;
            return false;
        });

        $this->interceptorService->onKernelController($controllerEvent);
        $this->assertEquals(
            false,
            $validatorCalled
        );
    }
}
