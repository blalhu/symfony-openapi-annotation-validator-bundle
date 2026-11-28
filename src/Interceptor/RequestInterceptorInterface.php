<?php

namespace Pelso\OpenAPIValidatorBundle\Interceptor;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

interface RequestInterceptorInterface
{
    public function onKernelController(FilterControllerEvent $filterControllerEvent);
}
