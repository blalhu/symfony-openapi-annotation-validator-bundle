<?php

namespace Pelso\OpenAPIValidatorBundle\Tests;

use Pelso\OpenAPIValidatorBundle\DependencyInjection\PelsoOpenAPIValidatorBundleExtension;
use Pelso\OpenAPIValidatorBundle\PelsoOpenAPIValidatorBundle;
use PHPUnit\Framework\TestCase;

class PelsoOpenAPIValidatorBundleTest extends TestCase
{
    public function testBundleClassExistence()
    {
        $this->assertEquals(
            PelsoOpenAPIValidatorBundleExtension::class,
            get_class((new PelsoOpenAPIValidatorBundle())->getContainerExtension())
        );
    }
}
