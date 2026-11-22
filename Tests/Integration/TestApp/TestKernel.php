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
    private $extensionConfig;

    public function setExtensionConfig(array $extensionConfig): void
    {
        $this->extensionConfig = $extensionConfig;
    }

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
        $loader->load(function (ContainerBuilder $containerBuilder) {
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
                $this->extensionConfig
            );
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

    protected function getContainerClass()
    {
        return parent::getContainerClass()
            . '_'
            . sha1(json_encode($this->extensionConfig));
    }
}
