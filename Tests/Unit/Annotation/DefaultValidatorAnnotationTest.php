<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Unit\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidator;
use PHPUnit\Framework\TestCase;

class DefaultValidatorAnnotationTest extends TestCase
{
    /** @var ValidatorAnnotationInterface $annotation */
    private $loadedAnnotation;

    protected function setUp()
    {
        parent::setUp();

        $checker = new DefaultValidatorAnnotationChecker();
        $reflectionClass = new \ReflectionClass($checker);
        $reader = new AnnotationReader();
        /** @var ValidatorAnnotationInterface $annotation */
        $this->loadedAnnotation = $reader->getClassAnnotation($reflectionClass, DefaultValidator::class);
    }

    public function testAnnotationValuePass(): void
    {
        $this->assertEquals(
            2,
            count($this->loadedAnnotation->getErrorActions())
        );
    }

    public function testNameGetter(): void
    {
        $this->assertEquals(
            'default',
            $this->loadedAnnotation->getProviderName()
        );
    }

    public function testDefaultErrorMessageFormatter(): void
    {
        $this->assertEquals(
            '@pelso.openapi_validator_bundle.validation_error_message_formatter.default',
            $this->loadedAnnotation->getErrorMessageFormatter()
        );
    }
}
