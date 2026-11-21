<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Unit\Provider;

use Pelso\OpenAPIValidatorBundle\Provider\JSONFileOpenAPIProvider;
use PHPUnit\Framework\TestCase;

class JSONFileOpenAPIProviderTest extends TestCase
{
    private $validJSON;

    protected function setUp()
    {
        parent::setUp();

        $this->validJSON = __DIR__.'/../resources/valid_openapi.json';
    }

    public function testNoException()
    {
        $this->assertEquals(JSONFileOpenAPIProvider::class, get_class(new JSONFileOpenAPIProvider($this->validJSON)));
    }
}
