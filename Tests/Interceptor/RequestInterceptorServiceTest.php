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
    /** @var ControllerEventResolver */
    private $controllerEventResolver;

    /** @var RequestValidator */
    private $requestValidator;

    /** @var OpenAPIProviderCollectionInterface */
    private $providerCollection;

    /** @var FilterControllerEvent */
    private $event;

    public function setUp()
    {
        $this->controllerEventResolver = $this->createMock(ControllerEventResolver::class);

        $this->requestValidator = $this->createMock(RequestValidator::class);

        $this->providerCollection = $this->createMock(OpenAPIProviderCollectionInterface::class);
        $this->providerCollection->method('get')
            ->willReturn($this->createMock(OpenAPIProviderInterface::class));

        $this->event = $this->createMock(FilterControllerEvent::class);
        $this->event->method('getRequest')->willReturn($this->createMock(Request::class));
        $this->event->method('isMasterRequest')->willReturn(true);
        $this->event->method('getController')->willReturn([]);
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
        $this->controllerEventResolver->method('resolveAsValidator')
            ->willReturnCallback(function () use (&$resolveAsValidatorCalled) {
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
        $this->requestValidator->method('validate')->willReturnCallback(function () use (&$validateCalled) {
            $validateCalled = true;
        });

        $interceptor = new RequestInterceptorService(
            $this->requestValidator,
            $this->providerCollection,
            $this->controllerEventResolver,
            ['@service.name']
        );


        $event = $this->createMock(FilterControllerEvent::class);
        $event->method('getRequest')->willReturn($this->createMock(Request::class));
        $event->method('isMasterRequest')->willReturn($isMasterRequestValue);
        $event->method('getController')->willReturn($getControllerValue);
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
        $this->controllerEventResolver->method('resolveAsValidator')
            ->willReturnCallback(function () use (&$resolveAsValidatorCalled) {
                $resolveAsValidatorCalled = true;

                throw new UndeterminableValidatorException();
            });

        $validateCalled = false;
        $this->requestValidator->method('validate')->willReturnCallback(function () use (&$validateCalled) {
            $validateCalled = true;
        });

        $interceptor = new RequestInterceptorService(
            $this->requestValidator,
            $this->providerCollection,
            $this->controllerEventResolver,
            ['@service.name']
        );

        $interceptor->onKernelController($this->event);

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
        $this->controllerEventResolver->method('resolveAsValidator')
            ->willReturnCallback(function () use (&$resolveAsValidatorCalled, &$getErrorActionsCalled) {
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

        $this->requestValidator->method('validate')
            ->willReturnCallback(function () use (&$validateCalled, $throwValidationErrorException) {
                $validateCalled = true;

                if ($throwValidationErrorException) {
                    throw new ValidationErrorException(new \Exception());
                }
            });

        $interceptor = new RequestInterceptorService(
            $this->requestValidator,
            $this->providerCollection,
            $this->controllerEventResolver,
            ['@service.name']
        );
        $interceptor->onKernelController($this->event);

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

    /**
     * @param array $argumentSettings
     * @param array $expectedErrorActions
     * @param bool $nonExistingErrorActionException
     *
     * @return void
     *
     * @throws NonExistingErrorActionException
     * @throws NonExistingValidatorProfileException
     * @throws \ReflectionException
     *
     * @dataProvider actionTriggeringDataProvider
     */
    public function testActionTriggering(
        array $argumentSettings,
        array $expectedErrorActions,
        bool $nonExistingErrorActionException
    ): void {
        $this->controllerEventResolver->method('resolveAsValidator')
            ->willReturnCallback(function () use ($argumentSettings) {

                return new class($argumentSettings) implements ValidatorAnnotationInterface
            {
                    private $argumentSettings;

                    public function __construct($argumentSettings)
                    {
                        $this->argumentSettings = $argumentSettings;
                    }

                    public function getProviderName(): string
                    {
                        return 'providerName';
                    }


                    public function getErrorActions(): array
                    {
                        return $this->argumentSettings;
                    }
                };
            });

        $this->requestValidator->method('validate')->willReturnCallback(function () {
            throw new ValidationErrorException(new \Exception());
        });

        $calledServiceNames = [];
        $interceptor = new RequestInterceptorService(
            $this->requestValidator,
            $this->providerCollection,
            $this->controllerEventResolver,
            [
                '@service.name1' => new TestAction('@service.name1', $calledServiceNames),
                '@service.name2' => new TestAction('@service.name2', $calledServiceNames),
                '@service.name3' => new TestAction('@service.name3', $calledServiceNames),
                '@service.name4' => new TestAction('@service.name4', $calledServiceNames),
            ]
        );

        if ($nonExistingErrorActionException) {
            $this->expectException(NonExistingErrorActionException::class);
        }

        $interceptor->onKernelController($this->event);

        if (!$nonExistingErrorActionException) {
            $this->assertEquals($expectedErrorActions, $calledServiceNames);
        }
    }

    public function actionTriggeringDataProvider(): array
    {
        return [
            'correctSelection' => [
                'argumentSettings' => ['@service.name2', '@service.name3'],
                'expectedErrorActions' => ['@service.name2', '@service.name3'],
                'expectedNonExistingErrorActionException' => false,
            ],
            'NonExistingErrorActionException' => [
                'argumentSettings' => ['@service.name2', '@service.name_non_existing'],
                'expectedErrorActions' => [],
                'expectedNonExistingErrorActionException' => true,
            ],
        ];
    }
}
