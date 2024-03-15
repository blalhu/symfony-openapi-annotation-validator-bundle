<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use PHPUnit\Framework\TestCase;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;

class AbstractOpenAPIProviderTest extends TestCase
{
    private const OPEN_API_ARRAY = [
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'Sample API',
            'description' => 'Optional multiline or single-line description in [CommonMark](http://commonmark.org/help/) or HTML.',
            'version' => '0.1.9',
        ],
        'servers' => [
            [
                'url' => 'http://api.example.com/v1',
                'description' => 'Optional server description, e.g. Main (production) server',
            ],
        ],
        'paths' => [
            '/users' => [
                'get' => [
                    'summary' => 'Returns a list of users.',
                    'description' => 'Optional extended description in CommonMark or HTML.',
                    'responses' => [
                        '200' => [
                            'description' => 'A JSON array of user names',
                            'content' => [
                                'application/json' => [
                                    'scheme' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'string',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /** @var AbstractOpenAPIProvider */
    private $providerClass;

    protected function setUp()
    {
        parent::setUp();

        $this->setProviderClass();
    }

    /**
     * @return void
     */
    private function setProviderClass(): void
    {
        $this->providerClass = new class(self::OPEN_API_ARRAY) extends AbstractOpenAPIProvider
        {
            public function __construct(array $openAPIArray)
            {
                parent::__construct($openAPIArray);
            }
        };
    }

    public function testValidScheme(): void
    {
        $this->assertTrue(
            is_subclass_of(
                $this->providerClass,
                AbstractOpenAPIProvider::class
            )
        );
    }

    public function testArrayGetter(): void
    {
        $this->assertEquals(
            self::OPEN_API_ARRAY,
            $this->providerClass->getArray()
        );
    }


    public function testValidatorGetter(): void
    {
        $this->assertEquals(
            ValidatorBuilder::class,
            get_class($this->providerClass->getValidatorBuilder())
        );
    }
}
