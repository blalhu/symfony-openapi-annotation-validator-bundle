<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\TestKernel;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExceptionActionTest extends ExtensionKernelTestCase
{

    protected static function getLoggerClass(): string
    {
        return LogActionRecordLogger::class;
    }

    public function testInvalidCase(): void
    {
        self::bootKernel();

        $request = Request::create(
            '/exception_action_annotation',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"deliberately_wrong_content":"example"}'
        );

        $this->expectException(ValidationErrorException::class);

        static::$kernel->handle(
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            false
        );
    }

    public function testValidCase(): void
    {
        self::bootKernel();

        $request = Request::create(
            '/exception_action_annotation',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"id": 1, "name": "sample_name", "date": "2026-09-12"}'
        );
        static::$kernel->handle($request);

        $this->assertEquals([], static::$kernel->getContainer()->get('logger')->getLogEntriesByLevel(LogLevel::ERROR));
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
