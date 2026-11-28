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

    private $containerSuffix;

    private $loggerClass = NullLogger::class;

    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        $this->containerSuffix = bin2hex(random_bytes(12));
    }

    public function setExtensionConfig(array $extensionConfig): void
    {
        $this->extensionConfig = $extensionConfig;
    }

    public function setLoggerClass(string $loggerClass): void
    {
        $this->loggerClass = $loggerClass;
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
            $containerBuilder->register('logger', $this->loggerClass);
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
            . '..'
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'test_artifacts'
            . DIRECTORY_SEPARATOR
            . 'Integration'
            . DIRECTORY_SEPARATOR
            . 'cache';
    }

    public function getLogDir()
    {
        return $this->getRootDir()
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'test_artifacts'
            . DIRECTORY_SEPARATOR
            . 'Integration'
            . DIRECTORY_SEPARATOR
            . 'logs';
    }

    protected function getContainerClass()
    {
        return parent::getContainerClass()
            . '_'
            . $this->containerSuffix;
    }
}
