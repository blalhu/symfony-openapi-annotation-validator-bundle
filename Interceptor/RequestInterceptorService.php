<?php

namespace Pelso\OpenAPIValidatorBundle\Interceptor;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use function Pelso\OpenAPIValidatorBundle\Service\dump;

class RequestInterceptorService implements RequestInterceptorInterface
{
    public function onKernelController(FilterControllerEvent $filterControllerEvent)
    {
        dump($filterControllerEvent);
    }
}