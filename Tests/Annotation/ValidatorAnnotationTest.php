<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use PHPUnit\Framework\TestCase;

class ValidatorAnnotationTest extends TestCase
{
    /** @var ValidatorAnnotationInterface $annotation */
    private $loadedAnnotation;

    protected function setUp()
    {
        parent::setUp();

        $checker = new ValidatorAnnotationChecker();
        $reflectionClass = new \ReflectionClass($checker);
        $reader = new AnnotationReader();
        /** @var ValidatorAnnotationInterface $annotation */
        $this->loadedAnnotation = $reader->getClassAnnotation($reflectionClass, Validator::class);
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
            'provider_name',
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

    public function testCustomErrorMessageFormatter(): void
    {
        $checker = new ValidatorAnnotationCheckerWithFormatter();
        $reflectionClass = new \ReflectionClass($checker);
        $reader = new AnnotationReader();
        /** @var ValidatorAnnotationInterface $annotation */
        $loadedAnnotation = $reader->getClassAnnotation($reflectionClass, Validator::class);

        $this->assertEquals(
            '@my.custom.message.formatter',
            $loadedAnnotation->getErrorMessageFormatter()
        );
    }
}
