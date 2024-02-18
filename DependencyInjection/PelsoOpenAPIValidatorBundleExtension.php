<?php

namespace Pelso\OpenAPIValidatorBundle\DependencyInjection;

use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorService;
use Pelso\OpenAPIValidatorBundle\Service\CheckerService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class PelsoOpenAPIValidatorBundleExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $container = $this->defineRequestInterceptor($configs, $container);
    }

    private function defineRequestInterceptor(array $configs, ContainerBuilder $container)
    {
        $container
            ->register('pelso.openapi_validator_bundle.request_interceptor')
            ->setClass(RequestInterceptorService::class)
            ->addTag('kernel.event_listener', ['event' => 'kernel.controller']);

        return $container;
    }
}
