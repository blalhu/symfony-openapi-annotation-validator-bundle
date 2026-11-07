<?php

namespace Pelso\OpenAPIValidatorBundle\Resolver;

use Doctrine\Common\Annotations\AnnotationReader;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingValidatorProfileException;
use Pelso\OpenAPIValidatorBundle\Exceptions\UndeterminableValidatorException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ControllerEventResolver
{
    /** @var ValidatorProfileResolver */
    private $validatorProfileResolver;

    /** @var array $validatorAnnotationClasses */
    private $validatorAnnotationClasses;

    public function __construct(
        ValidatorProfileResolver $validatorProfileResolver,
        array $validatorAnnotationClasses
    ) {
        $this->validatorProfileResolver = $validatorProfileResolver;
        $this->validatorAnnotationClasses = $validatorAnnotationClasses;
    }


    /**
     * @throws \ReflectionException
     * @throws NonExistingValidatorProfileException
     * @throws UndeterminableValidatorException
     */
    public function resolveAsValidator(
        FilterControllerEvent $event
    ): ValidatorAnnotationInterface {
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
            $annotation = $this->validatorProfileResolver
                ->resolveAsValidator($annotation);
        }

        if ($annotation === null) {
            throw new UndeterminableValidatorException();
        }

        return $annotation;
    }
}
