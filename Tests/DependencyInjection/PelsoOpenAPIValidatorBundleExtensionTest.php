<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\DependencyInjection;

use Pelso\OpenAPIValidatorBundle\DependencyInjection\PelsoOpenAPIValidatorBundleExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PelsoOpenAPIValidatorBundleExtensionTest extends TestCase
{
    public function testServiceRegister(): void
    {
        $containerBuilder = new ContainerBuilder();
        (new PelsoOpenAPIValidatorBundleExtension())->load([], $containerBuilder);

        $this->assertTrue(
            $containerBuilder->has('pelso.openapi_validator_bundle.request_interceptor')
        );
    }
}
