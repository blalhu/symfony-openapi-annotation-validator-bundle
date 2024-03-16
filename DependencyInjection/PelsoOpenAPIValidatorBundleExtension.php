<?php

namespace Pelso\OpenAPIValidatorBundle\DependencyInjection;

use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollection;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorService;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class PelsoOpenAPIValidatorBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new BundleConfiguration();
        $configs = $this->processConfiguration($configuration, $configs);

        $container = $this->defineRequestInterceptor($configs, $container);
        $container = $this->defineValidator($configs, $container);
        $container = $this->defineProviderCollection($configs, $container);
    }

    private function defineRequestInterceptor(array $configs, ContainerBuilder $container): ContainerBuilder
    {
        if ($configs['interceptor'] !== '@' . BundleConfiguration::REQUEST_INTERCEPTOR) {
            $container->setAlias(
                BundleConfiguration::REQUEST_INTERCEPTOR,
                preg_replace('/^\@/', '', $configs['interceptor'])
            );
        } else {
            $container
                ->register(BundleConfiguration::REQUEST_INTERCEPTOR)
                ->setClass(RequestInterceptorService::class)
                ->addTag('kernel.event_listener', ['event' => 'kernel.controller']);
        }

        return $container;
    }

    private function defineValidator(array $configs, ContainerBuilder $container): ContainerBuilder
    {
        if ($configs['validator'] !== '@' . BundleConfiguration::VALIDATOR) {
            $container->setAlias(
                BundleConfiguration::VALIDATOR,
                preg_replace('/^\@/', '', $configs['validator'])
            );
        } else {
            $container
                ->register(BundleConfiguration::VALIDATOR)
                ->setClass(RequestValidator::class);
        }

        return $container;
    }

    private function defineProviderCollection(array $configs, ContainerBuilder $container): ContainerBuilder
    {
        if ($configs['definition_provider_collection'] !== '@' . BundleConfiguration::PROVIDER_COLLECTION) {
            $container->setAlias(
                BundleConfiguration::PROVIDER_COLLECTION,
                preg_replace('/^\@/', '', $configs['definition_provider_collection'])
            );
        } else {
            $container
                ->register(BundleConfiguration::PROVIDER_COLLECTION)
                ->setClass(OpenAPIProviderCollection::class);
        }

        return $container;
    }
}
