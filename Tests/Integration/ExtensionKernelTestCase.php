<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration;

use Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\TestKernel;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class ExtensionKernelTestCase extends KernelTestCase
{
    private static $cacheCleared = false;

    abstract protected static function getExtensionConfig(): array;

    protected static function getLoggerClass(): string
    {
        return NullLogger::class;
    }

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
        static::$kernel->setLoggerClass(static::getLoggerClass());
        static::$kernel->boot();
    }

    protected function setUp()
    {
        parent::setUp();

        if (self::$cacheCleared) {
            return;
        }

        $cacheDir = static::createKernel()->getCacheDir();
        if (!is_dir($cacheDir)) {
            return;
        }

        $filesystem = new Filesystem();
        foreach (new \FilesystemIterator($cacheDir, \FilesystemIterator::SKIP_DOTS) as $entry) {
            if ($entry->getFilename() !== '.gitkeep') {
                $filesystem->remove($entry->getPathname());
            }
        }

        self::$cacheCleared = true;
    }
}
