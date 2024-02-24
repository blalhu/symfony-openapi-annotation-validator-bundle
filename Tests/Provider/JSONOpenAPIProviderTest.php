<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Provider;

use Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException;
use Pelso\OpenAPIValidatorBundle\Provider\JSONOpenAPIProvider;
use PHPUnit\Framework\TestCase;

class JSONOpenAPIProviderTest extends TestCase
{
    private $validJSON;
    private $inValidJSON;

    protected function setUp()
    {
        parent::setUp();

        $this->validJSON = file_get_contents(__DIR__.'/../resources/valid_openapi.json');
        $this->inValidJSON = file_get_contents(__DIR__.'/../resources/invalid_openapi.json');
    }

    public function testInvalidJson(): void
    {
        $this->expectException(InvalidOpenAPISchemeException::class);
        new JSONOpenAPIProvider($this->inValidJSON);
    }

    public function testValidJson(): void
    {
        $this->assertInstanceOf(
            JSONOpenAPIProvider::class,
            new JSONOpenAPIProvider($this->validJSON)
        );
    }
}
