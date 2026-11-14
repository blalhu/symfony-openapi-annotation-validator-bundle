<?php
//TODO: clean up this mess -- dry out the code
// TODO: Verify validation receives the exact request and selected provider.
// TODO: Verify error actions receive the original exception and controller event.

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Action\BadRequestResponseErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;
use Pelso\OpenAPIValidatorBundle\Action\ExceptionErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\HeaderNoticeErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\LogErrorAction;
use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidator;
use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollection;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollectionInterface;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingErrorActionException;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingValidatorProfileException;
use Pelso\OpenAPIValidatorBundle\Exceptions\UndeterminableValidatorException;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorInterface;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorService;
use Pelso\OpenAPIValidatorBundle\Provider\AbstractOpenAPIProvider;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Pelso\OpenAPIValidatorBundle\Resolver\ControllerEventResolver;
use Pelso\OpenAPIValidatorBundle\Resolver\ValidatorProfileResolver;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestInterceptorServiceTest extends TestCase
{
    private $interceptorService;

    private $controllerEvent;

    private $providerCollection;

    private $controllerEventResolver;

    private $validator;

    private $providerMock;

    private $badRequestErrorAction;
    private $exceptionErrorAction;
    private $headerNoticeErrorAction;
    private $logErrorAction;

    public function setUp()
    {
        $this->controllerEvent = $this->createMock(FilterControllerEvent::class);
        $this->controllerEvent->method('getRequest')->willReturn(new Request());
        $this->controllerEvent->method('isMasterRequest')->willReturn(true);

        $this->providerCollection = $this->createMock(OpenAPIProviderCollection::class);
        // Real resolver instance remains here; ControllerEventResolver is not mocked.
        $this->controllerEventResolver = new ControllerEventResolver(
            $this->createMock(ValidatorProfileResolver::class),
            [DefaultValidator::class, Validator::class, ValidatorProfile::class]
        );
        $this->validator = $this->createMock(RequestValidator::class);

        $this->badRequestErrorAction = $this->createMock(BadRequestResponseErrorAction::class);
        $this->exceptionErrorAction = $this->createMock(ExceptionErrorAction::class);
        $this->headerNoticeErrorAction = $this->createMock(HeaderNoticeErrorAction::class);
        $this->logErrorAction = $this->createMock(LogErrorAction::class);

        $this->interceptorService = new RequestInterceptorService(
            $this->validator,
            $this->providerCollection,
            // Usage: inject the resolver instance into the interceptor.
            $this->controllerEventResolver,
            [
                '@pelso.openapi_validator_bundle.error_action.bad_request_response' => $this->badRequestErrorAction,
                '@pelso.openapi_validator_bundle.error_action.exception' => $this->exceptionErrorAction,
                '@pelso.openapi_validator_bundle.error_action.header_notice' => $this->headerNoticeErrorAction,
                '@pelso.openapi_validator_bundle.error_action.log' => $this->logErrorAction,
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

    /**
     * @param bool $isMasterRequestValue
     * @param $getControllerValue
     * @param bool $expectedToCallResolveAsValidator
     * @param bool $expectedToCallValidate
     *
     * @return void
     *
     * @throws NonExistingErrorActionException
     * @throws NonExistingValidatorProfileException
     * @throws \ReflectionException
     *
     * @dataProvider requestContextCheckingDataProvider
     */
    public function testRequestContextChecking(
        bool $isMasterRequestValue,
        $getControllerValue,
        bool $expectedToCallResolveAsValidator,
        bool $expectedToCallValidate
    ): void {
        $resolveAsValidatorCalled = false;
        $controllerEventResolver = $this->createMock(ControllerEventResolver::class);
        $controllerEventResolver->method('resolveAsValidator')->willReturnCallback(function () use (&$resolveAsValidatorCalled) {
            $resolveAsValidatorCalled = true;

            return new class implements ValidatorAnnotationInterface
            {
                public function getProviderName(): string
                {
                    return 'providerName';
                }


                public function getErrorActions(): array
                {
                    return [];
                }
            };
        });

        $validateCalled = false;
        $requestValidator = $this->createMock(RequestValidator::class);
        $requestValidator->method('validate')->willReturnCallback(function () use (&$validateCalled) {
            $validateCalled = true;
        });

        $providerCollection = $this->createMock(OpenAPIProviderCollectionInterface::class);
        $providerCollection->method('get')->willReturn($this->createMock(OpenAPIProviderInterface::class));

        $interceptor = new RequestInterceptorService(
            $requestValidator,
            $providerCollection,
            $controllerEventResolver,
            ['@service.name']
        );

        $event = $this->createMock(FilterControllerEvent::class);
        $event->method('isMasterRequest')->willReturn($isMasterRequestValue);
        $event->method('getController')->willReturn($getControllerValue);
        $event->method('getRequest')->willReturn($this->createMock(Request::class));
        $interceptor->onKernelController($event);

        $this->assertEquals($expectedToCallResolveAsValidator, $resolveAsValidatorCalled);
        $this->assertEquals($expectedToCallValidate, $validateCalled);
    }

    public function requestContextCheckingDataProvider(): array
    {
        return [
            'NotMasterRequest__ValidController' => [
                'isMasterRequest' => false,
                'getController' => [],
                'expectedToCallResolveAsValidator' => false,
                'expectedToCallValidate' => false,
            ],
            'IsMasterRequest__InvalidController' => [
                'isMasterRequest' => true,
                'getController' => true,
                'expectedToCallResolveAsValidator' => false,
                'expectedToCallValidate' => false,
            ],
            'NotMasterRequest__InvalidController' => [
                'isMasterRequest' => false,
                'getController' => true,
                'expectedToCallResolveAsValidator' => false,
                'expectedToCallValidate' => false,
            ],
            'IsMasterRequest__ValidController' => [
                'isMasterRequest' => true,
                'getController' => [],
                'expectedToCallResolveAsValidator' => true,
                'expectedToCallValidate' => true,
            ],
        ];
    }

    public function testUndeterminableValidator(): void
    {
        $resolveAsValidatorCalled = false;
        $controllerEventResolver = $this->createMock(ControllerEventResolver::class);
        $controllerEventResolver->method('resolveAsValidator')->willReturnCallback(function () use (&$resolveAsValidatorCalled) {
            $resolveAsValidatorCalled = true;

            throw new UndeterminableValidatorException();
        });

        $validateCalled = false;
        $requestValidator = $this->createMock(RequestValidator::class);
        $requestValidator->method('validate')->willReturnCallback(function () use (&$validateCalled) {
            $validateCalled = true;
        });

        $providerCollection = $this->createMock(OpenAPIProviderCollectionInterface::class);
        $providerCollection->method('get')->willReturn($this->createMock(OpenAPIProviderInterface::class));

        $interceptor = new RequestInterceptorService(
            $requestValidator,
            $providerCollection,
            $controllerEventResolver,
            ['@service.name']
        );

        $event = $this->createMock(FilterControllerEvent::class);
        $event->method('isMasterRequest')->willReturn(true);
        $event->method('getController')->willReturn([]);
        $event->method('getRequest')->willReturn($this->createMock(Request::class));
        $interceptor->onKernelController($event);

        $this->assertTrue($resolveAsValidatorCalled);
        $this->assertFalse($validateCalled);
    }

    /**
     * @param bool $throwValidationErrorException
     * @param bool $expectedToCallValidate
     * @param bool $expectedGetErrorActionsCalled
     *
     * @return void
     *
     * @throws NonExistingErrorActionException
     * @throws NonExistingValidatorProfileException
     * @throws \ReflectionException
     *
     * @dataProvider validityHandlingDataProvider
     */
    public function testValidityHandling(
        bool $throwValidationErrorException,
        bool $expectedToCallValidate,
        bool $expectedGetErrorActionsCalled
    ): void {
        $resolveAsValidatorCalled = false;
        $getErrorActionsCalled = false;
        $controllerEventResolver = $this->createMock(ControllerEventResolver::class);
        $controllerEventResolver->method('resolveAsValidator')->willReturnCallback(function () use (&$resolveAsValidatorCalled, &$getErrorActionsCalled) {
            $resolveAsValidatorCalled = true;

            return new class($getErrorActionsCalled) implements ValidatorAnnotationInterface
            {
                private $getErrorActionsCalled;

                public function __construct(&$getErrorActionsCalled)
                {
                    $this->getErrorActionsCalled =& $getErrorActionsCalled;
                }

                public function getProviderName(): string
                {
                    return 'providerName';
                }


                public function getErrorActions(): array
                {
                    $this->getErrorActionsCalled = true;

                    return [];
                }
            };
        });

        $validateCalled = false;

        $providerCollection = $this->createMock(OpenAPIProviderCollectionInterface::class);
        $providerCollection->method('get')->willReturn($this->createMock(OpenAPIProviderInterface::class));

        $requestValidator = $this->createMock(RequestValidator::class);
        $requestValidator->method('validate')->willReturnCallback(function () use (&$validateCalled, $throwValidationErrorException) {
            $validateCalled = true;

            if ($throwValidationErrorException) {
                throw new ValidationErrorException(new \Exception());
            }
        });

        $interceptor = new RequestInterceptorService(
            $requestValidator,
            $providerCollection,
            $controllerEventResolver,
            ['@service.name']
        );
        $event = $this->createMock(FilterControllerEvent::class);
        $event->method('isMasterRequest')->willReturn(true);
        $event->method('getController')->willReturn([]);
        $event->method('getRequest')->willReturn($this->createMock(Request::class));
        $interceptor->onKernelController($event);

        $this->assertEquals($expectedToCallValidate, $validateCalled);
        $this->assertEquals($expectedGetErrorActionsCalled, $getErrorActionsCalled);
    }

    public function validityHandlingDataProvider(): array
    {
        return [
            'invalid' => [
                'throwValidationErrorException' => true,
                'expectedToCallValidate' => true,
                'expectedGetErrorActionsCalled' => true,
            ],
            'valid' => [
                'throwValidationErrorException' => false,
                'expectedToCallValidate' => true,
                'expectedGetErrorActionsCalled' => false,
            ],
        ];
    }

    public function testActionTriggering(): void
    {
        $resolveAsValidatorCalled = false;
        $getErrorActionsCalled = false;
        $controllerEventResolver = $this->createMock(ControllerEventResolver::class);
        $controllerEventResolver->method('resolveAsValidator')->willReturnCallback(function () use (&$resolveAsValidatorCalled, &$getErrorActionsCalled) {
            $resolveAsValidatorCalled = true;

            return new class($getErrorActionsCalled) implements ValidatorAnnotationInterface
            {
                private $getErrorActionsCalled;

                public function __construct(&$getErrorActionsCalled)
                {
                    $this->getErrorActionsCalled =& $getErrorActionsCalled;
                }

                public function getProviderName(): string
                {
                    return 'providerName';
                }


                public function getErrorActions(): array
                {
                    $this->getErrorActionsCalled = true;

                    return ['@service.name2', '@service.name3'];
                }
            };
        });

        $validateCalled = false;
        $requestValidator = $this->createMock(RequestValidator::class);
        $requestValidator->method('validate')->willReturnCallback(function () use (&$validateCalled) {
            $validateCalled = true;

            throw new ValidationErrorException(new \Exception());
        });

        $providerCollection = $this->createMock(OpenAPIProviderCollectionInterface::class);
        $providerCollection->method('get')->willReturn($this->createMock(OpenAPIProviderInterface::class));

        $calledServiceNames = [];
        $interceptor = new RequestInterceptorService(
            $requestValidator,
            $providerCollection,
            $controllerEventResolver,
            [
                '@service.name1' => new TestAction('@service.name1', $calledServiceNames),
                '@service.name2' => new TestAction('@service.name2', $calledServiceNames),
                '@service.name3' => new TestAction('@service.name3', $calledServiceNames),
                '@service.name4' => new TestAction('@service.name4', $calledServiceNames),
            ]
        );

        $event = $this->createMock(FilterControllerEvent::class);
        $event->method('isMasterRequest')->willReturn(true);
        $event->method('getController')->willReturn([]);
        $event->method('getRequest')->willReturn($this->createMock(Request::class));
        $interceptor->onKernelController($event);

        $this->assertEquals(['@service.name2', '@service.name3'], $calledServiceNames);


        $controllerEventResolver = $this->createMock(ControllerEventResolver::class);
        $controllerEventResolver->method('resolveAsValidator')->willReturnCallback(function () use (&$resolveAsValidatorCalled, &$getErrorActionsCalled) {
            $resolveAsValidatorCalled = true;

            return new class($getErrorActionsCalled) implements ValidatorAnnotationInterface
            {
                private $getErrorActionsCalled;

                public function __construct(&$getErrorActionsCalled)
                {
                    $this->getErrorActionsCalled =& $getErrorActionsCalled;
                }

                public function getProviderName(): string
                {
                    return 'providerName';
                }


                public function getErrorActions(): array
                {
                    $this->getErrorActionsCalled = true;

                    return ['@service.name2', '@service.name_non_existing'];
                }
            };
        });

        $calledServiceNames = [];
        $interceptor = new RequestInterceptorService(
            $requestValidator,
            $providerCollection,
            $controllerEventResolver,
            [
                '@service.name1' => new TestAction('@service.name1', $calledServiceNames),
                '@service.name2' => new TestAction('@service.name2', $calledServiceNames),
                '@service.name3' => new TestAction('@service.name3', $calledServiceNames),
                '@service.name4' => new TestAction('@service.name4', $calledServiceNames),
            ]
        );

        $event = $this->createMock(FilterControllerEvent::class);
        $event->method('isMasterRequest')->willReturn(true);
        $event->method('getController')->willReturn([]);
        $event->method('getRequest')->willReturn($this->createMock(Request::class));
        $this->expectException(NonExistingErrorActionException::class);
        $interceptor->onKernelController($event);
    }
}
