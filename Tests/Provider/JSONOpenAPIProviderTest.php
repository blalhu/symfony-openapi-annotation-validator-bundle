<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Provider;

use Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException;
use Pelso\OpenAPIValidatorBundle\Exceptions\ResourceParseError;
use Pelso\OpenAPIValidatorBundle\Provider\JSONOpenAPIProvider;
use PHPUnit\Framework\TestCase;

class JSONOpenAPIProviderTest extends TestCase
{
    private $invalidJSON;
    private $validJSON;
    private $invalidOpenAPIJSON;

    protected function setUp()
    {
        parent::setUp();

        $this->invalidJSON = file_get_contents(__DIR__.'/../resources/invalid.json');
        $this->validJSON = file_get_contents(__DIR__.'/../resources/valid_openapi.json');
        $this->invalidOpenAPIJSON = file_get_contents(__DIR__.'/../resources/invalid_openapi.json');
    }

    public function testInvalidJson(): void
    {
        $this->expectException(ResourceParseError::class);
        new JSONOpenAPIProvider($this->invalidJSON);
    }

    public function testInvalidOpenAPIJson(): void
    {
        $this->expectException(InvalidOpenAPISchemeException::class);
        new JSONOpenAPIProvider($this->invalidOpenAPIJSON);
    }

    public function testValidJson(): void
    {
        $this->assertInstanceOf(
            JSONOpenAPIProvider::class,
            new JSONOpenAPIProvider($this->validJSON)
        );
    }
}
