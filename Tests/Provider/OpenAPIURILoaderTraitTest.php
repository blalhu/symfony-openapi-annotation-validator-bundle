<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Provider;

use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIURILoaderTrait;
use PHPUnit\Framework\TestCase;

class OpenAPIURILoaderTraitTest extends TestCase
{
    /** @var \stdClass */
    private $mockClass;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockClass = new class
        {
            use OpenAPIURILoaderTrait;

            /**
             * @param string $fileURI
             * @return void
             */
            public function checkOpen(string $fileURI): string
            {
                return $this->loadOpenApiURIResource($fileURI);
            }
        };
    }

    /**
     * @return void
     */
    public function testNonExistingFileExtension()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->mockClass->checkOpen(__DIR__.'/../resources/non_existing_file.some_extension');
    }

    /**
     * @return void
     */
    public function testExistingFileExtension()
    {
        $content = $this->mockClass->checkOpen(__DIR__.'/../resources/existing_file.some_extension');
        $this->assertEquals('content_a', $content);
    }
}