<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp;

use Pelso\OpenAPIValidatorBundle\PelsoOpenAPIValidatorBundle;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{

    /**
     * @inheritDoc
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new PelsoOpenAPIValidatorBundle(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function(ContainerBuilder $containerBuilder) {
            $containerBuilder->register('logger', NullLogger::class);
            $containerBuilder->loadFromExtension(
                'framework',
                [
                    'secret' => 'integrationtestsecret',
                    'router' => [
                        'resource' => $this->getRootDir()
                            . DIRECTORY_SEPARATOR
                            . 'config/routing.yaml'
                    ],
                ]
            );
            $containerBuilder->loadFromExtension(
                'pelso_open_api_validator_bundle',
                [
                    'openapi_yaml_list' => [
                        [
                            'name' => 'default',
                            'uri' => '%kernel.root_dir%/Resources/openapi.yaml',
                        ]
                    ]
                ]);
        });
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return $this->getRootDir()
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'cache';
    }

    public function getLogDir()
    {
        return $this->getRootDir()
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'logs';
    }
}