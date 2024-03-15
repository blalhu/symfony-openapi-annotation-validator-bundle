<?php

namespace Pelso\OpenAPIValidatorBundle\Interceptor;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestInterceptorService implements RequestInterceptorInterface
{
    public function onKernelController(FilterControllerEvent $filterControllerEvent)
    {
        $filterControllerEvent->getRequest()->setLocale('test_value');
    }
}
