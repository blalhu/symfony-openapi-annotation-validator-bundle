<?php

namespace Pelso\OpenAPIValidatorBundle\Interceptor;

use Doctrine\Common\Annotations\AnnotationReader;
use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollectionInterface;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidatorInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestInterceptorService implements RequestInterceptorInterface
{
    /** @var RequestValidatorInterface */
    private $requestValidator;

    /** @var OpenAPIProviderCollectionInterface */
    private $providerCollection;

    private $validatorAnnotationClasses = [];

    /** @var ErrorActionInterface[] */
    private $errorActionServices = [];

    public function __construct(
        RequestValidatorInterface $requestValidator,
        OpenAPIProviderCollectionInterface $providerCollection,
        array $validatorAnnotationServices,
        array $errorActionServices
    ) {
        $this->requestValidator = $requestValidator;
        $this->providerCollection = $providerCollection;
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
        if (!$filterControllerEvent->isMasterRequest()) {
            return;
        }

        if (!is_array($filterControllerEvent->getController())) {
            return;
        }

        [$controllerObject, $actionName] = $filterControllerEvent->getController();
        $controllerClassName = get_class($controllerObject);

        $reflectionClass = new \ReflectionClass($controllerClassName);
        $reflectionMethod = new \ReflectionMethod($controllerClassName.'::'.$actionName);
        $reader = new AnnotationReader();

        /** @var ValidatorAnnotationInterface|null $annotation */
        $annotation = null;
        foreach ($this->validatorAnnotationClasses as $annotationClass) {
            $annotation = $reader->getClassAnnotation($reflectionClass, $annotationClass);
            if ($annotation !== null) {
                break;
            }
            $annotation = $reader->getMethodAnnotation($reflectionMethod, $annotationClass);
            if ($annotation !== null) {
                break;
            }
        }

        if ($annotation === null) {
            return;
        }

        if ($this->requestValidator->validate($filterControllerEvent->getRequest())) {
            return;
        }

        foreach ($annotation->getErrorActions() as $errorActionServiceId) {
            $this->errorActionServices[$errorActionServiceId]->triggerAction();
        }
    }
}
