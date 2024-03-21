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
        $filterControllerEvent = $this->createMock(FilterControllerEvent::class);
        $filterControllerEvent->method('getRequest')->willReturn(new Request());
        $filterControllerEvent->method('getController')->willReturn([new ValidatorAnnotationChecker(), 'defaultAction']);
        $filterControllerEvent->method('isMasterRequest')->willReturn(true);

        $service = new RequestInterceptorService(
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
        $service->onKernelController($filterControllerEvent);
        $this->assertEquals(
            'en',
            $filterControllerEvent->getRequest()->getLocale()
        );
    }
}
