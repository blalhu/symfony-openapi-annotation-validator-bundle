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

    public function testOverriddenServiceRegister(): void
    {
        $containerBuilder = new ContainerBuilder();
        (new PelsoOpenAPIValidatorBundleExtension())->load(
            [
                'pelso_open_api_validator_bundle' => [
                    'interceptor' => '@app.api_interceptor',
                    'validator' => '@app.api_validator',
                    'definition_provider_collection' => '@app.api_provider_collection',
                ],
            ],
            $containerBuilder
        );

        $this->assertTrue(
            $containerBuilder->has('pelso.openapi_validator_bundle.request_interceptor')
        );
    }
}
