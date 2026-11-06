<?php

namespace Pelso\OpenAPIValidatorBundle\Interceptor;

use Doctrine\Common\Annotations\AnnotationReader;
use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollectionInterface;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingErrorActionException;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingValidatorProfileException;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidatorInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestInterceptorService implements RequestInterceptorInterface
{
    /** @var RequestValidatorInterface */
    private $requestValidator;

    /** @var OpenAPIProviderCollectionInterface */
    private $providerCollection;

    private $configs = [];

    private $validatorAnnotationClasses = [];

    /** @var ErrorActionInterface[] */
    private $errorActionServices = [];

    public function __construct(
        RequestValidatorInterface $requestValidator,
        OpenAPIProviderCollectionInterface $providerCollection,
        array $configs,
        array $validatorAnnotationServices,
        array $errorActionServices
    ) {
        $this->requestValidator = $requestValidator;
        $this->providerCollection = $providerCollection;
        $this->configs = $configs;
        $this->setValidatorAnnotationClasses($validatorAnnotationServices);
        $this->errorActionServices = $errorActionServices;
    }

    private function setValidatorAnnotationClasses(array $validatorAnnotationServices): void
    {
        foreach ($validatorAnnotationServices as $service) {
            $this->validatorAnnotationClasses[] = $service;
        }
    }

    public function onKernelController(FilterControllerEvent $filterControllerEvent)
    {
        if (!$filterControllerEvent->isMasterRequest()
            || !is_array($filterControllerEvent->getController())
        ) {
            return;
        }

        /** @var ValidatorAnnotationInterface|null $annotation */
        $annotation = $this->controllerEventToValidatorAnnotation($filterControllerEvent);

        if ($annotation === null) {
            return;
        }

        try {
            $this->requestValidator->validate(
                $filterControllerEvent->getRequest(),
                $this->providerCollection->get($annotation->getProviderName())
            );

            return;
        } catch (ValidationErrorException $exception) {
            foreach ($annotation->getErrorActions() as $errorActionServiceId) {
                if (!array_key_exists($errorActionServiceId, $this->errorActionServices)) {
                    throw new NonExistingErrorActionException();
                }
                $this->errorActionServices[$errorActionServiceId]->triggerAction(
                    $exception,
                    $filterControllerEvent
                );
            }
        }
    }

    /**
     * @throws \ReflectionException
     * @throws NonExistingValidatorProfileException
     */
    private function controllerEventToValidatorAnnotation(
        FilterControllerEvent $event
    ): ?ValidatorAnnotationInterface {
        [$controllerObject, $actionName] = $event->getController();
        $controllerClassName = get_class($controllerObject);

        $reflectionClass = new \ReflectionClass($controllerClassName);
        $reflectionMethod = new \ReflectionMethod($controllerClassName.'::'.$actionName);
        $reader = new AnnotationReader();

        $annotation = null;
        foreach ($this->validatorAnnotationClasses as $annotationClass) {
            $annotation = $reader->getMethodAnnotation($reflectionMethod, $annotationClass);
            if ($annotation !== null) {
                break;
            }
            $annotation = $reader->getClassAnnotation($reflectionClass, $annotationClass);
            if ($annotation !== null) {
                break;
            }
        }

        if ($annotation instanceof ValidatorProfile) {
            $annotation = $this->validatorProfileConverter($annotation);
        }

        return $annotation;
    }

    /**
     * @throws NonExistingValidatorProfileException
     */
    private function validatorProfileConverter(
        ValidatorProfile $validatorProfile
    ): ValidatorAnnotationInterface {
        $profileConfig = [];
        foreach ($this->configs['profiles'] as $profile) {
            if ($profile['profile_name'] === $validatorProfile->profileName) {
                $profileConfig = $profile;
            }
        }
        if (empty($profileConfig)) {
            throw new NonExistingValidatorProfileException();
        }

        $annotation = new Validator();
        $annotation->providerName = $profileConfig['provider_name'];
        $annotation->errorActions = $profileConfig['error_actions'];

        return $annotation;
    }
}
