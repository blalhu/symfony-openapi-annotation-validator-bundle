<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration;

use Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ExtensionKernelTestCase extends KernelTestCase
{
    abstract protected static function getExtensionConfig(): array;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function bootKernel(
        array $options = []
    ) {
        static::ensureKernelShutdown();

        static::$kernel = static::createKernel($options);
        static::$kernel->setExtensionConfig(static::getExtensionConfig());
        static::$kernel->boot();
    }
}
