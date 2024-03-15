<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Provider;

use Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException;
use Pelso\OpenAPIValidatorBundle\Exceptions\ResourceParseError;
use Pelso\OpenAPIValidatorBundle\Provider\YAMLOpenAPIProvider;
use PHPUnit\Framework\TestCase;

class YAMLOpenAPIProviderTest extends TestCase
{
    private $validYaml;
    private $invalidYaml;
    private $inValidOpenAPIYaml;

    protected function setUp()
    {
        parent::setUp();

        $this->invalidYaml = file_get_contents(__DIR__.'/../resources/invalid.yaml');
        $this->validYaml = file_get_contents(__DIR__.'/../resources/valid_openapi.yaml');
        $this->inValidOpenAPIYaml = file_get_contents(__DIR__.'/../resources/invalid_openapi.yaml');
    }

    public function testInvalidYaml(): void
    {
        $this->expectException(ResourceParseError::class);
        new YAMLOpenAPIProvider($this->invalidYaml);
    }
    public function testInvalidOpenAPIYaml(): void
    {
        $this->expectException(InvalidOpenAPISchemeException::class);
        new YAMLOpenAPIProvider($this->inValidOpenAPIYaml);
    }

    public function testValidYaml(): void
    {
        $this->assertInstanceOf(
            YAMLOpenAPIProvider::class,
            new YAMLOpenAPIProvider($this->validYaml)
        );
    }
}
