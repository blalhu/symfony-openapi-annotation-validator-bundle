<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use PHPUnit\Framework\TestCase;

class YAMLFileOpenAPIProviderTest extends TestCase
{
    private $validYAML;

    protected function setUp()
    {
        parent::setUp();

        $this->validYAML = __DIR__.'/../resources/valid_openapi.yaml';
    }

    public function testNoException()
    {
        $this->assertEquals(YAMLFileOpenAPIProvider::class, get_class(new YAMLFileOpenAPIProvider($this->validYAML)));
    }
}
