<?php

namespace Pelso\OpenAPIValidatorBundle\Interceptor;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollectionInterface;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingErrorActionException;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingValidatorProfileException;
use Pelso\OpenAPIValidatorBundle\Exceptions\UndeterminableValidatorException;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Pelso\OpenAPIValidatorBundle\Resolver\ControllerEventResolver;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidatorInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestInterceptorService implements RequestInterceptorInterface
{
    /** @var RequestValidatorInterface */
    private $requestValidator;

    /** @var OpenAPIProviderCollectionInterface */
    private $providerCollection;

    /** @var ControllerEventResolver */
    private $controllerEventResolver;

    /** @var ErrorActionInterface[] */
    private $errorActionServices = [];

    public function __construct(
        RequestValidatorInterface $requestValidator,
        OpenAPIProviderCollectionInterface $providerCollection,
        ControllerEventResolver $controllerEventResolver,
        array $errorActionServices
    ) {
        $this->requestValidator = $requestValidator;
        $this->providerCollection = $providerCollection;
        $this->controllerEventResolver = $controllerEventResolver;
        $this->errorActionServices = $errorActionServices;
    }

    /**
     * @throws \ReflectionException
     * @throws NonExistingErrorActionException
     * @throws NonExistingValidatorProfileException
     */
    public function onKernelController(FilterControllerEvent $filterControllerEvent)
    {
        if (!$filterControllerEvent->isMasterRequest()
            || !is_array($filterControllerEvent->getController())
        ) {
            return;
        }

        try {
            /** @var ValidatorAnnotationInterface|null $annotation */
            $annotation = $this->controllerEventResolver
                ->resolveAsValidator($filterControllerEvent);
        } catch (UndeterminableValidatorException $exception) {
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
}
