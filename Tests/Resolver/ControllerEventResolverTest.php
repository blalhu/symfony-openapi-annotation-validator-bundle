<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Resolver;

use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidator;
use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingValidatorProfileException;
use Pelso\OpenAPIValidatorBundle\Exceptions\UndeterminableValidatorException;
use Pelso\OpenAPIValidatorBundle\Resolver\ControllerEventResolver;
use Pelso\OpenAPIValidatorBundle\Resolver\ValidatorProfileResolver;
use Pelso\OpenAPIValidatorBundle\Tests\Interceptor\ClassAndMethodAnnotationController;
use Pelso\OpenAPIValidatorBundle\Tests\Interceptor\ClassAnnotationController;
use Pelso\OpenAPIValidatorBundle\Tests\Interceptor\DefaultAnnotationController;
use Pelso\OpenAPIValidatorBundle\Tests\Interceptor\MethodAnnotationController;
use Pelso\OpenAPIValidatorBundle\Tests\Interceptor\ProfileAnnotationController;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Rules\Control;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ControllerEventResolverTest extends TestCase
{
    /** @var ValidatorProfileResolver $validatorProfileResolver */
    private $validatorProfileResolver;

    /** @var array $validatorAnnotationClasses */
    private $validatorAnnotationClasses;

    public function setUp()
    {
        parent::setUp();

        $this->validatorProfileResolver = $this
            ->createMock(ValidatorProfileResolver::class);
        $this->validatorAnnotationClasses = [
            DefaultValidator::class,
            Validator::class,
            ValidatorProfile::class,
        ];
    }

    /**
     * @dataProvider annotationDataProvider
     *
     * @param Controller $controller
     * @param string $actionName
     * @param string $validatorClass
     * @param string $providerName
     * @param array $errorActions
     *
     * @return void
     *
     * @throws NonExistingValidatorProfileException
     * @throws UndeterminableValidatorException
     * @throws \ReflectionException
     */
    public function testAnnotation(
        Controller $controller,
        string $actionName,
        string $validatorClass,
        string $providerName,
        array $errorActions
    ): void {
        $resolver = new ControllerEventResolver(
            $this->validatorProfileResolver,
            $this->validatorAnnotationClasses
        );
        $validator = $resolver->resolveAsValidator(
            $this->getControllerEventMock(
                $controller,
                $actionName
            )
        );

        $this->assertEquals($validatorClass, get_class($validator));
        $this->assertEquals($providerName, $validator->getProviderName());
        $this->assertEquals($errorActions, $validator->getErrorActions());
    }

    public function annotationDataProvider(): array
    {
        return [
            'classValidator' => [
                new ClassAnnotationController(),
                'defaultAction',
                Validator::class,
                'class',
                ['@pelso.openapi_validator_bundle.error_action.log']
            ],
            'methodValidator' => [
                new MethodAnnotationController(),
                'defaultAction',
                Validator::class,
                'method',
                ['@pelso.openapi_validator_bundle.error_action.log']
            ],
            'classAndMethodValidator' => [
                new ClassAndMethodAnnotationController(),
                'defaultAction',
                Validator::class,
                'method',
                ['@pelso.openapi_validator_bundle.error_action.log']
            ],
            'defaultValidator' => [
                new DefaultAnnotationController(),
                'defaultAction',
                DefaultValidator::class,
                'default',
                [
                    '@pelso.openapi_validator_bundle.error_action.bad_request_response',
                    '@pelso.openapi_validator_bundle.error_action.log',
                ]
            ],
        ];
    }

    public function testValidatorProfile(): void
    {
        $validatorProfileResolver = new ValidatorProfileResolver([
            'profiles' => [[
                'profile_name' => 'v_profile_name',
                'provider_name' => 'provider1',
                'error_actions' => [
                    '@action1',
                    '@action2',
                ],
            ]]
        ]);
        $resolver = new ControllerEventResolver(
            $validatorProfileResolver,
            $this->validatorAnnotationClasses
        );
        $validator = $resolver->resolveAsValidator(
            $this->getControllerEventMock(
                new ProfileAnnotationController(),
                'profileAnnotationAction'
            )
        );

        $this->assertEquals(Validator::class, get_class($validator));
        $this->assertEquals('provider1', $validator->getProviderName());
        $this->assertEquals(['@action1', '@action2'], $validator->getErrorActions());
    }

    private function getControllerEventMock(
        Controller $controller,
        string $actionName
    ): FilterControllerEvent {
        $controllerEvent = $this->createMock(FilterControllerEvent::class);
        $controllerEvent->method('getRequest')->willReturn(new Request());
        $controllerEvent->method('isMasterRequest')->willReturn(true);
        $controllerEvent->method('getController')->willReturn([
            $controller,
            $actionName
        ]);

        return $controllerEvent;
    }
}
