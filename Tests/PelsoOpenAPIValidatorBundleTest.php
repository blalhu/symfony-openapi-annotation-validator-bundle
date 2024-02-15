<?php

namespace Pelso\OpenAPIValidatorBundle\Tests;

use Pelso\OpenAPIValidatorBundle\PelsoOpenAPIValidatorBundle;
use PHPUnit\Framework\TestCase;

class PelsoOpenAPIValidatorBundleTest extends TestCase
{
    public function testBundleClassExistence()
    {
        $this->assertEquals(true, class_exists(PelsoOpenAPIValidatorBundle::class));
    }
}