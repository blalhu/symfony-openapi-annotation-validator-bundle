<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Provider;

use Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Pelso\OpenAPIValidatorBundle\Provider\YAMLOpenAPIProvider;
use PHPUnit\Framework\TestCase;

class YAMLOpenAPIProviderTest extends TestCase
{
    private $validYaml;
    private $inValidYaml;

    protected function setUp()
    {
        parent::setUp();

        $this->validYaml = file_get_contents(__DIR__.'/../resources/valid_openapi.yaml');
        $this->inValidYaml = file_get_contents(__DIR__.'/../resources/invalid_openapi.yaml');
    }

    public function testInvalidYaml(): void
    {
        $this->expectException(InvalidOpenAPISchemeException::class);
        new YAMLOpenAPIProvider($this->inValidYaml);
    }

    public function testValidYaml(): void
    {
        $this->assertInstanceOf(
            YAMLOpenAPIProvider::class,
            new YAMLOpenAPIProvider($this->validYaml)
        );
    }
}
