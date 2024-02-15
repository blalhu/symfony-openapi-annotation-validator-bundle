<?php

namespace Pelso\OpenAPIValidatorBundle\DependencyInjection;

use Pelso\OpenAPIValidatorBundle\Service\CheckerService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class PelsoOpenAPIValidatorBundleExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $container
            ->register('pelso.openapi_validator_bundle.checker')
            ->setClass(CheckerService::class)
            ->addArgument(new Reference('logger'));
    }
}