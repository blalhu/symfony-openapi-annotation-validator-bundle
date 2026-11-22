<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration;

use Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class DefaultMethodAnnotationTest extends ExtensionKernelTestCase
{
    public function testInvalidCase(): void
    {
        self::bootKernel();

        $request = Request::create(
            '/default_method_annotation',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"deliberately_wrong_content":"example"}'
        );
        $response = static::$kernel->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testValidCase(): void
    {
        self::bootKernel();

        $request = Request::create(
            '/default_method_annotation',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"id": 1, "name": "sample_name", "date": "2026-09-12"}'
        );
        $response = static::$kernel->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    protected static function getExtensionConfig(): array
    {
        return [
            'openapi_yaml_list' => [
                [
                    'name' => 'default',
                    'uri' => '%kernel.root_dir%/Resources/openapi.yaml',
                ]
            ]
        ];
    }
}
